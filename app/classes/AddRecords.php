<?php
//require ('EdukacjaCl');
class AddRecords
{
	public $courses;
	public $c_days;
	public $c_kinds;
	public $c_lectures;
	public $c_places;
	public $c_teachers;
	public $c_hours;
	public $c_codes;
	public $c_spaces;

	public $unique_hours = [];

	public function AddRecords($courses = array())
	{		
		 $this->courses = $courses;

		$i = 0;
		$j = 0;

		foreach($this->courses as $course)
		{
			$this->c_lectures['name'][] = $course['nazwa'];
			$this->c_lectures['code'][] = $course['kod'];
			$this->c_lectures['kind'][] = $course['dane'][0]['rodzaj'];
			foreach ($course['dane'] as $termin) {
				$this->c_days[] = $termin['dzien'];
				$this->c_places['building'][$j] = $termin['budynek'];
				$this->c_places['room'][$j] = $termin['sala'];
				$this->c_teachers[] = $termin['prowadzacy'];
				$this->c_hours['start'][$j] = $termin['start'];
				$this->c_hours['finish'][$j] = $termin['koniec'];
				$this->c_codes[] = $termin['kod'];
				$this->c_kinds[] = $termin['rodzaj'];
				$this->c_spaces['taken'][$j] = $termin['zajete'];
				$this->c_spaces['all'][$j] = $termin['wszystkie'];
				$j++;
			}
			$i++;
		}
	}

	//metoda ma mi zwrócić tablice bez powtórzeń
	private function createUnique1D(&$c_array = array())
	{
		//iteruje po całej tablicy
		for($i = 0; $i < count($c_array); $i++)
		{
			$unique = true;
			//sprawdzam pozostałe elementy, o wyższych elementach
			for($j = $i + 1; $j < count($c_array); $j++)
			{
				//jeśli coś znajdę to ustawiam flagę na false i przerywam pętle
				if($c_array[$i] == $c_array[$j])
				{
					$unique = false;	
				} 
			}
			if($unique)
			{
				$unique_array[] = $c_array[$i];
			}
		}
		return $unique_array;
	}
	
	//metoda dodaje dane do tabeli o nazwie w $table_name, tabela musi mieć tylko jedną kolumne o 
	//domyślnej nazwie name i znaczniki czasu, w gre wchodzą tablice: days, kinds, codes, teachers
	public function addOne($table_name, $array, $column_name = "name")
	{		
		//tablica unikalnych rekordów
		$unique_records = $this->createUnique1D($array);
		//pobieram rekordy z bazy, dostanę tylko te, które są tam już zapisane
		$records_from_base = DB::table($table_name)->whereIn($column_name, $unique_records)->get();
		//tablica rekordów, które chce zapisać do bazy
		$records_to_base;
		//licznik rekordów, które chce zapisać do bazy
		$k = 0;
		//porównuje unikalne rekordy, z danymi z bazy
		//jeśli mam jakieś dane z bazy
		if(count($records_from_base) > 0)
		{
			//dla każdego unikalnego, muszę sprawdzić wszystkie rekordy z bazy
			for($i = 0; $i < count($unique_records); $i++)
			{
				//ustawiam flage unikalności na true
				$unique = true;
				for($j = 0; $j < count($records_from_base); $j++)
				{
					//jeśli wynik porównania będzie true to ustawiam flagę unikalności na false 
					//i przerywam pętle
					if($unique_records[$i] == $records_from_base[$j]->$column_name) 
					{
						$unique = false;
						break;
					}
				}
				//sprawdzam zawartość flagi
				if($unique)
				{
					//dodaje do tablicy dane dnia, muszę dodać też znaczniki czasu
					//ponieważ metoda insert ich nie ustawia
					$records_to_base[$k][$column_name] = $unique_records[$i];
					$records_to_base[$k]['created_at'] = new DateTime;
    				$records_to_base[$k]['updated_at'] = $records_to_base[$k]['created_at'];
    				//zwiększam licznik
    				$k++;
				}
			}
		}
		//jeśli odpowiedź z bazy jest pusta, do bazy dodam wszystkie unikalne rekordy
		else
		{
			foreach($unique_records as $record)
			{
				$records_to_base[$k][$column_name] = $record;
				$records_to_base[$k]['created_at'] = new DateTime;
    			$records_to_base[$k]['updated_at'] = $records_to_base[$k]['created_at'];
    			$k++;
			}
		}
		//jeśli ta tablica nie jest pusta, to jej zawartość dodaje do bazy
		if(!empty($records_to_base)) 
		{
			//mogę dostać wyjątek, bo założyłem klucz unique na kolumne name
			try
			{
				DB::table($table_name)->insert($records_to_base);
			}
			//jeśli dostane wyjątek, to ktoś musiał dodać coś w między czasie
			//więc od nowa wyznaczam dane do dodania do bazy
			catch(Exception $e)
			{
				$this->addOne($table_name, $array, $column_name = "name");
			}			
		}
	}

	//metoda ma mi zwrócić tablice bez powtórzeń
	public function createUnique2D(&$c_array, $keys)
	{
		//iteruje jakby po każdym wierszu
		for($i = 0; $i < count($c_array[$keys[0]]); $i++)
		{
			//sprawdzam pozostałe elementy, o wyższych elementach
			for($j = $i + 1; $j < count($c_array[($keys[0])]); $j++)
			{
				$repeat = 0;
				//iteruje po kolumnach
				for($k = 0; $k < count($keys); $k++) 
				{
					//jeśli choć jedna kolumna się różni to wiadomo, że nie ma powtorzenia
					if($c_array[$keys[$k]][$i] == $c_array[$keys[$k]][$j])
					{
						$repeat ++;						
					}
					;
				}
				//jeśli wszsytkie kolumny są równe to pzerywam sprawdzanie tego elementu
				if($repeat == count($keys)) break;			 
			}
			if($repeat < count($keys))
				{
					//muszę przekopiować wszystkie kolumny
					for($k = 0; $k < count($keys); $k++) 
					{
						$unique_array[$keys[$k]][] = $c_array[$keys[$k]][$i];
					}
				}
		}
		return $unique_array;
	}

	//metoda dodaje dane do tabeli o nazwie w $table_name, tabela musi mieć tylko jedną kolumne o 
	//domyślnej nazwie name i znaczniki czasu, w gre wchodzą tablice: days, kinds, codes, teachers
	public function addTwo($table_name, $array, $columns_names)
	{		
		//tablica unikalnych rekordów
		$unique_records = $this->createUnique2D($array, $columns_names);
		//pobieram rekordy z bazy, dostanę tylko te, które są tam już zapisane
		$records_from_base = DB::table($table_name)->whereIn($columns_names[0], $unique_records[$columns_names[0]])
												->whereIn($columns_names[0], $unique_records[$columns_names[0]])->get();
		//tablica rekordów, które chce zapisać do bazy
		$records_to_base;
		//licznik rekordów, które chce zapisać do bazy
		$k = 0;
		//porównuje unikalne rekordy, z danymi z bazy
		//jeśli mam jakieś dane z bazy
		if(count($records_from_base) > 0)
		{
			//dla każdego unikalnego, muszę sprawdzić wszystkie rekordy z bazy
			for($i = 0; $i < count($unique_records); $i++)
			{
				//ustawiam flage unikalności na true
				$unique = true;
				for($j = 0; $j < count($records_from_base); $j++)
				{
					//jeśli wynik porównania będzie true to ustawiam flagę unikalności na false 
					//i przerywam pętle
					if($unique_records[$columns_names[0]][$i] == $records_from_base[$j]->$columns_names[0]
						&& $unique_records[$columns_names[0]][$i] == $records_from_base[$j]->$columns_names[0])
					{
						$unique = false;
						break;
					}
				}
				//sprawdzam zawartość flagi
				if($unique)
				{
					//dodaje do tablicy dane dnia, muszę dodać też znaczniki czasu
					//ponieważ metoda insert ich nie ustawia
					$records_to_base[$k][$columns_names[0]] = $unique_records[$columns_names[0]][$i];
					$records_to_base[$k][$columns_names[1]] = $unique_records[$columns_names[1]][$i];
					$records_to_base[$k]['created_at'] = new DateTime;
    				$records_to_base[$k]['updated_at'] = $records_to_base[$k]['created_at'];
    				//zwiększam licznik
    				$k++;
				}
			}
		}
		//jeśli odpowiedź z bazy jest pusta, do bazy dodam wszystkie unikalne rekordy
		else
		{
			for($i = 0; $i < count($unique_records[$columns_names[0]]); $i++)
			{
				$records_to_base[$i][$columns_names[0]] = $unique_records[$columns_names[0]][$i];
				$records_to_base[$i][$columns_names[1]] = $unique_records[$columns_names[1]][$i];
				$records_to_base[$i]['created_at'] = new DateTime;
    			$records_to_base[$i]['updated_at'] = $records_to_base[$i]['created_at'];
			}
		}
		//jeśli ta tablica nie jest pusta, to jej zawartość dodaje do bazy
		if(!empty($records_to_base)) 
		{
			//mogę dostać wyjątek, bo założyłem klucz unique na kolumne name
			try
			{
				DB::table($table_name)->insert($records_to_base);
			}
			//jeśli dostane wyjątek, to ktoś musiał dodać coś w między czasie
			//więc od nowa wyznaczam dane do dodania do bazy
			catch(Exception $e)
			{
				$this->addTwo($table_name, $array, $columns_names);
			}			
		}
	}



	public function getKind($termin)
	{
		$kind = Kind::where('name', $termin["rodzaj"])->first();
		if(! $kind)
		{
			$kind = new Kind();
            $kind->name = $termin["rodzaj"];
            $kind->save();
		}
		return $kind;
		//return $this->kinds[$this->course["rodzaj"]];
	}

	public function getTeacher($termin)
	{
		$teacher = Teacher::where('name', $termin["prowadzacy"])->first();
		if(! $teacher)
		{
			$teacher = new Teacher();
            $teacher->name = $termin["prowadzacy"];
            $teacher->save();
		}
		return $teacher;
	}

	public function getHour($termin)
	{
		//$hour = Hour::where('start', $termin["start"])->where('finish', $termin["koniec"])->first();
		//if(! $hour)
		{
			$hour = new Hour();
            $hour->start = $termin["start"];
            $hour->finish = $termin["koniec"];
            try
            {
            	$hour->save();
            }
            catch(Exception $e)
            {
            	$hour = Hour::where('start', $termin["start"])->where('finish', $termin["koniec"])->first();
            }
            
		}
		return $hour;
	}

	public function getPlace($termin)
	{
		$place = Place::where('building', $termin["budynek"])->where('room', $termin["sala"])->first();
		if(! $place)
		{
			$place = new Place();
            $place->building = $termin["budynek"];
            $place->room = $termin["sala"];
            $place->save();
		}
		return $place;
	}

	public function getCode($termin)
	{
		$code = Code::where('name', $termin["kod"])->first();
		if(! $code)
		{
			$code = new Code();
            $code->name = $termin["kod"];
            $code->save();
		}
		return $code;
	}

	public function getSpace($termin)
	{
		$space = Space::where('taken', $termin["zajete"])->where('all', $termin["wszystkie"])->first();
		if(! $space)
		{
			$space = new Space();
            $space->taken = $termin["zajete"];
            $space->all = $termin["wszystkie"];
            $space->save();
		}
		return $space;
	}

	public function getTerm($termin)
	{
		$place = $this->getPlace($termin);
        $hour = $this->getHour($termin);
        $teacher = $this->getTeacher($termin);
        $day = $this->getDay($termin);
        $code = $this->getCode($termin);
        $space = $this->getSpace($termin);
        $term = Term::where('day', $day)
                                    ->where('teacher_id', $teacher->id)
                                    ->where('hour_id', $hour->id)
                                    ->where('place_id', $place->id)
                                    ->where('space_id', $space->id)
                                    ->where('code_id', $code->id)
                                    ->where('week', $termin["tydzien"])->first();

        if(! $term)
        {
            $term = new Term();
            $term->week = $termin["tydzien"];
            $term->day()->associate($day);
            $term->hour()->associate($hour);
            $term->place()->associate($place);
            $term->teacher()->associate($teacher);
            $term->code()->associate($code);
            $term->space()->associate($space);
            $term->save();
        }

        return $term;
	}

	public function getLecture()
	{
		$kind = $this->getKind($this->course['dane'][0]);

		$lecture = Lecture::where('name', $this->course['nazwa'])
							->where('kind_id', $kind->id)
							->where('code', $this->course['kod'])->first();

		if(! $lecture)
        {
            $lecture = new Lecture();
            $lecture->name = $this->course["nazwa"];
            $lecture->code = $this->course["kod"];
            $lecture->kind()->associate($kind);
            $lecture->save();
        }
        foreach ($this->course['dane'] as $termin) {
        	$term = $this->getTerm($termin);
        	$lecture->terms()->save($term);
        }
	}

	public function addUserLecture($user)
	{
			$lecture = $this->getLecture();
			if($lecture != null)
			{
				$ul = $user->with(array('lectures'=>function($query){
					$query->where('lecture_id', 'lecture->id');
				}))->first();

				if(!isset($ul->lectures[0]))
				{
					$lecture = $this->getLecture();
					// $user->lectures()->attach($lecture->id, array('semestr'=>$this->course["semestr"]));
					return array($lecture->id,array('semestr'=>$this->course["semestr"]));
					// // $lecture->users()->attach($user, array('semestr'=>$this->course["semestr"]));
				}
			}			
			return null;
			//return $lecture->users;
	}





}

?>