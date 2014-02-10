<?php
//require ('EdukacjaCl');
class AddRecords
{
	public $courses;
	public $c_days;
	public $c_lectures;
	public $c_places;
	public $c_teachers;
	public $c_hours;
	public $c_codes;
	public $c_spaces;
	public $c_weeks;

	public $unique_hours = [];

	public function AddRecords($courses = array())
	{		
		 $this->courses = $courses;

		foreach($this->courses as $course)
		{			
			foreach ($course['dane'] as $termin) {
				$this->c_days[] = $termin['dzien'];
				$this->c_places['building'][] = $termin['budynek'];
				$this->c_places['room'][] = $termin['sala'];
				$this->c_teachers[] = $termin['prowadzacy'];
				$this->c_hours['start'][] = $termin['start'].":00";
				$this->c_hours['finish'][] = $termin['koniec'].":00";
				$this->c_codes[] = $termin['kod'];
				$this->c_spaces['taken'][] = $termin['zajete'];
				$this->c_spaces['all'][] = $termin['wszystkie'];
				$this->c_weeks[] = $termin['tydzien'];
				$this->c_lectures['name'][] = $course['nazwa'];
				$this->c_lectures['code'][] = $course['kod'];
				$this->c_lectures['kind'][] = $termin['rodzaj'];
			}
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

	//metoda ma mi zwrócić tablice bez powtórzeń, tablica musi być dwuwymiarowa, ale nie ważne
	//ile ma kolumn
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

	//metoda dodaje dane do tabeli o nazwie w $table_name, tabela musi mieć dwie kolumny 
	//o nazwach przekazanych w $columns_names
	// i znaczniki czasu, w gre wchodzą tablice: places, hours, spaces
	public function addTwo($table_name, $array, $columns_names)
	{		
		//tablica unikalnych rekordów
		$unique_records = $this->createUnique2D($array, $columns_names);
		//pobieram rekordy z bazy, dostanę tylko te, które są tam już zapisane
		$records_from_base = DB::table($table_name)->whereIn($columns_names[0], $unique_records[$columns_names[0]])
												->whereIn($columns_names[1], $unique_records[$columns_names[1]])->get();
		//tablica rekordów, które chce zapisać do bazy
		$records_to_base;
		//licznik rekordów, które chce zapisać do bazy
		$k = 0;
		//porównuje unikalne rekordy, z danymi z bazy
		//jeśli mam jakieś dane z bazy

		if(count($records_from_base) > 0)
		{
			//dla każdego unikalnego, muszę sprawdzić wszystkie rekordy z bazy
			for($i = 0; $i < count($unique_records[$columns_names[0]]); $i++)
			{
				//ustawiam flage unikalności na true
				$unique = true;
				for($j = 0; $j < count($records_from_base); $j++)
				{
					//jeśli wynik porównania będzie true to ustawiam flagę unikalności na false 
					//i przerywam pętle
					if($unique_records[$columns_names[0]][$i] == $records_from_base[$j]->$columns_names[0]
						&& $unique_records[$columns_names[1]][$i] == $records_from_base[$j]->$columns_names[1])
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
				print_r($record_to_base);
				echo "<br> <br>";
			}
			//jeśli dostane wyjątek, to ktoś musiał dodać coś w między czasie
			//więc od nowa wyznaczam dane do dodania do bazy
			catch(Exception $e)
			{
				$this->addTwo($table_name, $array, $columns_names);
			}		
		}
	}

	public function addLectures()
	{
		$this->addOne("kinds", $this->c_lectures['kind']);
		$kinds = DB::table('kinds')->whereIn('name', $this->c_lectures['kind'])->get();
		for($i = 0; $i < count($this->c_lectures['name']); $i++)
		{
			for ($k=0; $k < count($kinds); $k++) 
			{ 
				if($this->c_lectures['kind'][$i] == $kinds[$k]->name)
				{
					$this->c_lectures['kind_id'][$i] = $kinds[$k]->id;
				}
			}
		}
		//tablica unikalnych rekordów
		$unique_lectures = $this->createUnique2D($this->c_lectures, array("name", "code", "kind", "kind_id"));
		//pobieram rekordy z bazy, dostanę tylko te, które są tam już zapisane
		$lectures_from_base = DB::table('lectures')->whereIn("name", $unique_lectures["name"])
												->whereIn("code", $unique_lectures["code"])
												->whereIn("kind_id", $unique_lectures["kind_id"])->get();
		//tablica rekordów, które chce zapisać do bazy
		$lectures_to_base;
		//licznik rekordów, które chce zapisać do bazy
		$k = 0;
		//porównuje unikalne rekordy, z danymi z bazy
		//jeśli mam jakieś dane z bazy
		if(count($lectures_from_base) > 0)
		{
			//dla każdego unikalnego, muszę sprawdzić wszystkie rekordy z bazy
			for($i = 0; $i < count($unique_lectures); $i++)
			{
				//ustawiam flage unikalności na true
				$unique = true;
				for($j = 0; $j < count($lectures_from_base); $j++)
				{
					//jeśli wynik porównania będzie true to ustawiam flagę unikalności na false 
					//i przerywam pętle
					if($unique_lectures['name'][$i] == $lectures_from_base[$j]->name
						&& $unique_lectures['code'][$i] == $lectures_from_base[$j]->code
						&& $unique_lectures['kind_id'][$i] == $lectures_from_base[$j]->kind_id)
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
					$lectures_to_base[$k]['name'] = $unique_lectures['name'][$i];
					$lectures_to_base[$k]['code'] = $unique_lectures['code'][$i];
					$lectures_to_base[$k]['kind_id'] = $unique_lectures['kind_id'][$i];
					$lectures_to_base[$k]['created_at'] = new DateTime;
    				$lectures_to_base[$k]['updated_at'] = $lectures_to_base[$k]['created_at'];
    				//zwiększam licznik
    				$k++;
				}
			}
		}
		//jeśli odpowiedź z bazy jest pusta, do bazy dodam wszystkie unikalne rekordy
		else
		{
			for($i = 0; $i < count($unique_lectures['name']); $i++)
			{
					$lectures_to_base[$i]['name'] = $unique_lectures['name'][$i];
					$lectures_to_base[$i]['code'] = $unique_lectures['code'][$i];
					$lectures_to_base[$i]['kind_id'] = $unique_lectures['kind_id'][$i];
					$lectures_to_base[$i]['created_at'] = new DateTime;
    				$lectures_to_base[$i]['updated_at'] = $lectures_to_base[$k]['created_at'];
			}
		}
		//jeśli ta tablica nie jest pusta, to jej zawartość dodaje do bazy
		if(!empty($lectures_to_base)) 
		{
			//mogę dostać wyjątek, bo założyłem klucz unique na kolumne name
			try
			{
				DB::table('lectures')->insert($lectures_to_base);
			}
			//jeśli dostane wyjątek, to ktoś musiał dodać coś w między czasie
			//więc od nowa wyznaczam dane do dodania do bazy
			catch(Exception $e)
			{
				$this->addLectures();
			}		
		}
		
	}

	public function addTerms($semestr)
	{
		$this->addLectures();
		$this->addOne("days", $this->c_days);
        $this->addOne("teachers", $this->c_teachers);
        $this->addOne("codes", $this->c_codes);
        $this->addTwo('hours', $this->c_hours, array("start", "finish"));
        $this->addTwo('places', $this->c_places, array("building", "room"));
        $this->addTwo('spaces', $this->c_spaces, array("taken", "all"));
        $lectures = DB::table('lectures')->whereIn('code', $this->c_lectures['code'])->get();
        $days = DB::table('days')->whereIn('name', $this->c_days)->get();
        $teachers = DB::table('teachers')->whereIn('name', $this->c_teachers)->get();
        $codes = DB::table('codes')->whereIn('name', $this->c_codes)->get();
        $hours = DB::table('hours')->whereIn('start', $this->c_hours['start'])
        							->whereIn('finish', $this->c_hours['finish'])->get();
        $places = DB::table('places')->whereIn('building', $this->c_places['building'])
        							->whereIn('room', $this->c_places['room'])->get();
        $spaces = DB::table('spaces')->whereIn('taken', $this->c_spaces['taken'])
        							->whereIn('all', $this->c_spaces['all'])->get();

        for($i = 0; $i < count($this->c_lectures['name']); $i++)
		{
			for ($k=0; $k < count($lectures); $k++) 
			{ 
				if($this->c_lectures['code'][$i] == $lectures[$k]->code)
				{
					$c_terms['lecture_id'][] = $lectures[$k]->id;
					break;
				}
			}
			for ($k=0; $k < count($days); $k++) 
			{ 
				if($this->c_days[$i] == $days[$k]->name)
				{
					$c_terms['day_id'][] = $days[$k]->id;
					break;
				}
			}
			for ($k=0; $k < count($teachers); $k++) 
			{ 
				if($this->c_teachers[$i] == $teachers[$k]->name)
				{
					$c_terms['teacher_id'][] = $teachers[$k]->id;
					break;
				}
			}
			for ($k=0; $k < count($codes); $k++) 
			{ 
				if($this->c_codes[$i] == $codes[$k]->name)
				{
					$c_terms['code_id'][] = $codes[$k]->id;
					break;
				}
			}
			for ($k=0; $k < count($hours); $k++) 
			{ 
				if($this->c_hours['start'][$i] == $hours[$k]->start
					&& $this->c_hours['finish'][$i] == $hours[$k]->finish)
				{
					$c_terms['hour_id'][] = $hours[$k]->id;
					break;
				}
			}
			for ($k=0; $k < count($places); $k++) 
			{ 
				if($this->c_places['building'][$i] == $places[$k]->building
					&& $this->c_places['room'][$i] == $places[$k]->room)
				{
					$c_terms['place_id'][] = $places[$k]->id;
					break;
				}
			}
			for ($k=0; $k < count($spaces); $k++) 
			{ 
				if($this->c_spaces['taken'][$i] == $spaces[$k]->taken
					&& $this->c_spaces['all'][$i] == $spaces[$k]->all)
				{
					$c_terms['space_id'][] = $spaces[$k]->id;
					break;
				}
			}
		}
		$c_terms['week'] = $this->c_weeks;

		//pobieram rekordy z bazy, dostanę tylko te, które są tam już zapisane
		$terms_from_base = DB::table('terms')->whereIn("lecture_id", $c_terms["lecture_id"])
												->whereIn("day_id", $c_terms["day_id"])
												->whereIn("teacher_id", $c_terms["teacher_id"])
												->whereIn("code_id", $c_terms["code_id"])
												->whereIn("hour_id", $c_terms["hour_id"])
												->whereIn("place_id", $c_terms["place_id"])
												->whereIn("space_id", $c_terms["space_id"])
												->whereIn("week", $c_terms["week"])->get();
		//tablica rekordów, które chce zapisać do bazy
		$terms_to_base;
		//licznik rekordów, które chce zapisać do bazy
		$k = 0;
		//porównuje unikalne rekordy, z danymi z bazy
		//jeśli mam jakieś dane z bazy
		if(count($terms_from_base) > 0)
		{
			//dla każdego unikalnego, muszę sprawdzić wszystkie rekordy z bazy
			for($i = 0; $i < count($c_terms); $i++)
			{
				//ustawiam flage unikalności na true
				$unique = true;
				for($j = 0; $j < count($terms_from_base); $j++)
				{
					//jeśli wynik porównania będzie true to ustawiam flagę unikalności na false 
					//i przerywam pętle
					if($c_terms['lecture_id'][$i] == $terms_from_base[$j]->lecture_id
						&& $c_terms['day_id'][$i] == $terms_from_base[$j]->day_id
						&& $c_terms['teacher_id'][$i] == $terms_from_base[$j]->teacher_id
						&& $c_terms['code_id'][$i] == $terms_from_base[$j]->code_id
						&& $c_terms['hour_id'][$i] == $terms_from_base[$j]->hour_id
						&& $c_terms['place_id'][$i] == $terms_from_base[$j]->place_id
						&& $c_terms['space_id'][$i] == $terms_from_base[$j]->space_id
						&& $c_terms['week'][$i] == $terms_from_base[$j]->week)
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
					$terms_to_base[$k]['lecture_id'] = $c_terms['lecture_id'][$i];
					$terms_to_base[$k]['day_id'] = $c_terms['day_id'][$i];
					$terms_to_base[$k]['teacher_id'] = $c_terms['teacher_id'][$i];
					$terms_to_base[$k]['code_id'] = $c_terms['code_id'][$i];
					$terms_to_base[$k]['hour_id'] = $c_terms['hour_id'][$i];
					$terms_to_base[$k]['place_id'] = $c_terms['place_id'][$i];
					$terms_to_base[$k]['space_id'] = $c_terms['space_id'][$i];
					$terms_to_base[$k]['week'] = $c_terms['week'][$i];
					$terms_to_base[$k]['created_at'] = new DateTime;
    				$terms_to_base[$k]['updated_at'] = $terms_to_base[$k]['created_at'];
    				//zwiększam licznik
    				$k++;
				}
			}
		}
		//jeśli odpowiedź z bazy jest pusta, do bazy dodam wszystkie unikalne rekordy
		else
		{
			for($i = 0; $i < count($c_terms['lecture_id']); $i++)
			{
					$terms_to_base[$i]['lecture_id'] = $c_terms['lecture_id'][$i];
					$terms_to_base[$i]['day_id'] = $c_terms['day_id'][$i];
					$terms_to_base[$i]['teacher_id'] = $c_terms['teacher_id'][$i];
					$terms_to_base[$i]['code_id'] = $c_terms['code_id'][$i];
					$terms_to_base[$i]['hour_id'] = $c_terms['hour_id'][$i];
					$terms_to_base[$i]['place_id'] = $c_terms['place_id'][$i];
					$terms_to_base[$i]['space_id'] = $c_terms['space_id'][$i];
					$terms_to_base[$i]['week'] = $c_terms['week'][$i];
					$terms_to_base[$i]['created_at'] = new DateTime;
    				$terms_to_base[$i]['updated_at'] = $terms_to_base[$i]['created_at'];
			}
		}
		//jeśli ta tablica nie jest pusta, to jej zawartość dodaje do bazy
		if(!empty($terms_to_base)) 
		{
			//mogę dostać wyjątek, bo założyłem klucz unique na kolumne name
			try
			{
				DB::table('terms')->insert($terms_to_base);
			}
			//jeśli dostane wyjątek, to ktoś musiał dodać coś w między czasie
			//więc od nowa wyznaczam dane do dodania do bazy
			catch(Exception $e)
			{
				$this->addTerms();
			}	
			for($i = 0; $i < count($terms_to_base); $i++)
			{
				
					$new_terms['lecture_id'][$i] = $terms_to_base[$i]['lecture_id'];
					$new_terms['day_id'][$i] = $terms_to_base[$i]['day_id'];
					$new_terms['teacher_id'][$i] = $terms_to_base[$i]['teacher_id'];
					$new_terms['code_id'][$i] = $terms_to_base[$i]['code_id'];
					$new_terms['hour_id'][$i] = $terms_to_base[$i]['hour_id'];
					$new_terms['place_id'][$i] = $terms_to_base[$i]['place_id'];
					$new_terms['space_id'][$i] = $terms_to_base[$i]['space_id'];
					$new_terms['week'][$i] = $terms_to_base[$i]['week'];
			}
			$terms_from_base = DB::table('terms')->whereIn("lecture_id", $new_terms["lecture_id"])
												->whereIn("day_id", $new_terms["day_id"])
												->whereIn("teacher_id", $new_terms["teacher_id"])
												->whereIn("code_id", $new_terms["code_id"])
												->whereIn("hour_id", $new_terms["hour_id"])
												->whereIn("place_id", $new_terms["place_id"])
												->whereIn("space_id", $new_terms["space_id"])
												->whereIn("week", $new_terms["week"])->get();
			for($i = 0; $i < count($terms_from_base); $i++)
			{
				$records_to_base[$terms_from_base[$i]->id] = array('semestr'=>$semestr, 'year'=>date("Y"));
			}
			return $records_to_base;					
		}
		else
		{
			return null;
		}		
	}

	public function addFieldUser($field, $user)
	{
			$field_from_base = DB::table('fields')->where('name', $field)->get();
			if(empty($fields_from_base))
			{
				$field_from_base = new Field();
				$field_from_base->name = $field;
				$field_from_base->save();
			}					
			$user->fields()->sync(array($user->id));
	}
}

?>