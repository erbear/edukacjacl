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
		$this->days = array("pn" => 1, "wt" => 2, "śr" => 3, "cz" => 4, "pt" => 5);
		$this->kinds = array("Wykład" => 1, "Zajęcia laboratoryjne" => 2, "Ćwiczenia" => 3);
	}
	public function getDay()
	{
		//return $this->days[$this->course["dzien"]];
		$day = Day::where('name', $this->course["dzien"])->first();
		if(! $day)
		{
			$day = new Day();
            $day->name = $this->course["dzien"];
            $day->save();
		}
		return $day;
	}

	public function getKind()
	{
		$kind = Kind::where('name', $this->course["rodzaj"])->first();
		if(! $kind)
		{
			$kind = new Kind();
            $kind->name = $this->course["rodzaj"];
            $kind->save();
		}
		return $kind;
		//return $this->kinds[$this->course["rodzaj"]];
	}

	public function getTeacher()
	{
		$teacher = Teacher::where('name', $this->course["prowadzacy"])->first();
		if(! $teacher)
		{
			$teacher = new Teacher();
            $teacher->name = $this->course["prowadzacy"];
            $teacher->save();
		}
		return $teacher;
	}

	public function getHour()
	{
		$hour = Hour::where('start', $this->course["start"])->where('finish', $this->course["koniec"])->first();
		if(! $hour)
		{
			$hour = new Hour();
            $hour->start = $this->course["start"];
            $hour->finish = $this->course["koniec"];
            $hour->save();
		}
		return $hour;
	}

	public function getPlace()
	{
		$place = Place::where('building', $this->course["budynek"])->where('room', $this->course["sala"])->first();
		if(! $place)
		{
			$place = new Place();
            $place->building = $this->course["budynek"];
            $place->room = $this->course["sala"];
            $place->save();
		}
		return $place;
	}

	public function getLecture()
	{
		$place = $this->getPlace();
        $hour = $this->getHour();
        $teacher = $this->getTeacher();
        $day = $this->getDay();
        $kind = $this->getKind();
        $lecture = DB::table('lectures')->where('name', $this->course["nazwa"])
                                                                        ->where('day_id', $day->id)
                                                                        ->where('kind_id', $kind->id)
                                                                        ->where('teacher_id', $teacher->id)
                                                                        ->where('hour_id', $hour->id)
                                                                        ->where('place_id', $place->id)->first();
        if(! $lecture)
        {
            $lecture = new Lecture();
            $lecture->name = $this->course["nazwa"];
            $lecture->day()->associate($day);
            $lecture->hour()->associate($hour);
            $lecture->kind()->associate($kind);
            $lecture->place()->associate($place);
            $lecture->teacher()->associate($teacher);
            $lecture->save();
        }

        return $lecture;
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