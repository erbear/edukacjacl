<?php
class PlanController extends BaseController
{
	public function getIndex()
	{

        $edukacja = new EdukacjaCl($login, $password);
        $courses = $edukacja->getPlan();


        foreach ($courses as $course)
        {
            $lecture = Lecture::where('name', $course['nazwa'])->get();
            foreach ($lecture as $l){
                echo $l->name;
            }
            // echo '<br>';
            // $day = new Day();
            // $day->name = $course["dzien"];
            // $day->save();
            // $hour= new Hour();
            // $hour->start = $course["start"];
            // $hour->finish = $course["koniec"];
            // $hour->save();
            // $kind = new Kind();
            // $kind->name = $course["rodzaj"];
            // $kind->save();
            // $place = new Place();
            // $place->building = $course["budynek"];
            // $place->room = $course["sala"];
            // $place->save();
            // $teacher = new Teacher();
            // $teacher->name = $course["prowadzacy"];
            // $teacher->save();
            // $lecture = new Lecture();
            // $lecture->name = $course["nazwa"];
            // $lecture->day()->associate($day);
            // $lecture->hour()->associate($hour);
            // $lecture->kind()->associate($kind);
            // $lecture->place()->associate($place);
            // $lecture->teacher()->associate($teacher);
            // $lecture->save();
        }
		


	}
	public function getMyPlan(){
		$lecture = Lecture::where('user_id', Auth::user()->id)->get();//nie jestem pewny czy to bedzie smigac, nie mam uzytkownika

		View::make('plan.getplan', ['lecture'=>$lecture]);
	}
	public function getPlan($user){

	}
}