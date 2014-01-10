<?php
class PlanController extends BaseController
{
	public function getIndex()
	{
        Cache::forget('queries');
        $edukacja = new EdukacjaCl(Auth::user()->login, Crypt::decrypt(Auth::user()->password));
        $courses = $edukacja->getPlan();

        $user = Auth::user();
        $tablica = [];
        foreach ($courses as $course)
        {
            $adder = new AddRecord($course);
            //tworzysz tablice z zajeciami
            $lecture = $adder->addUserLecture(Auth::user());
            $tab = array($lecture[0]=>$lecture[1]);
            array_push($tablica, $tab[$lecture[0]]);

        }
        $user->lectures()->sync($tablica);
        echo Cache::get('queries'). "<br>";
        if (Auth::user()){
            return 'jej!';
        } else {
            return 'nie jej...';
        }
		


	}
	public function getMyPlan(){
        Cache::forget('queries');
		$lectures = Auth::user()->lectures()->with(['day','hour','kind', 'teacher','place'])->get();
		echo Cache::get('queries'). "<br>";
        //return $lectures;
        return View::make('plan.myplan', ['lectures'=>$lectures]);
	}
	public function getPlan($user){
        
        return $user;
	}
}