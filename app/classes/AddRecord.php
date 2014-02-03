<?php
//require ('EdukacjaCl');
class AddRecord
{
	private $course;
	private $days;
	private $kinds;
	public function AddRecord($course)
	{		
		$this->course = $course;
	}
	public function getDay($termin)
	{
		$day = Day::where('name', $termin["dzien"])->first();
		if(! $day)
		{
			$day = new Day();
            $day->name = $termin["dzien"];
            $day->save();
		}
		return $day;
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
		$hour = Hour::where('start', $termin["start"])->where('finish', $termin["koniec"])->first();
		if(! $hour)
		{
			$hour = new Hour();
            $hour->start = $termin["start"];
            $hour->finish = $termin["koniec"];
            $hour->save();
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
        $term = Term::where('day_id', $day->id)
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