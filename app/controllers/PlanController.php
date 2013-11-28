<?php
class PlanController extends BaseController
{
	public function getIndex()
	{

        $edukacja = new EdukacjaCl(Auth::user()->login, Crypt::decrypt(Auth::user()->password));
        $courses = $edukacja->getPlan();


        foreach ($courses as $course)
        {
            $adder = new AddRecord($course);
            $adder->addUserLecture(Auth::user());

        }
        if (Auth::user()){
            return 'jej!';
        } else {
            return 'nie jej...';
        }
		


	}
	public function getMyPlan(){
		$lectures = Auth::user()->lectures()->with(['day','hour','kind', 'teacher','place'])->get();
		return View::make('plan.myplan', ['lectures'=>$lectures]);
	}
	public function getPlan($user){
        
        return $user;
	}
}