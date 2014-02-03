<?php
class PlanController extends BaseController
{
	public function getIndex()
	{
        Cache::forget('queries');
        // $edukacja = new EdukacjaCl();
        // $courses = $edukacja->getPlan();
        // $user = Auth::user();
        // $tablica = array();
        // foreach ($courses as $course)
        // {
        //     $adder = new AddRecord($course);
        //     //tworzysz tablice z zajeciami
        //     $lecture = $adder->addUserLecture(Auth::user());
        //     $tab = array($lecture[0]=>$lecture[1]);
        //     array_push($tablica, $tab[$lecture[0]]);

        // }
        // $user->lectures()->sync($tablica);
        // echo Cache::get('queries'). "<br>";
        // if (Auth::user()){
        //     return 'jej!';
        // } else {
        //     return 'nie jej...';
        // }

        $user = Auth::user();
        $edukacja = new EdukacjaCl($user->login, Crypt::decrypt($user->password));
        $courses = $edukacja->idzDoPrzegladaniaKursow();

        foreach ($courses as $key => $course) {

            // echo $course['nazwa']. " " . $course['kod']. " <br>";
            // foreach($course['dane'] as $key2 => $dane)
            // {
            //     print_r($dane);
            //     echo '<br>';
            //     // foreach ($dane as $key3 => $dana) {
            //     //     echo($key3. "   ". $dana);
            //     //      echo '<br>';
            //     // }
                
            //     echo '<br> <br>';
            // }
            // echo '<br> <br> <br>';

            $adder = new AddRecord($course);
            $adder->getLecture();


        }

        echo Cache::get('queries'). "<br>";

	}

    public function getAll()
    {

        $user = Auth::user();
        $edukacja = new EdukacjaCl($user->login, Crypt::decrypt($user->password));
        $courses = $edukacja->idzDoPrzegladaniaKursow();

         foreach ($courses as $key => $course) {

            echo $course['nazwa']. " " . $course['kod']. " <br>";
            foreach($course['dane'] as $key2 => $dane)
            {
                print_r($dane);
                echo '<br>';
                // foreach ($dane as $key3 => $dana) {
                //     echo($key3. "   ". $dana);
                //      echo '<br>';
                // }
                
                echo '<br> <br>';
            }
            echo '<br> <br> <br>';

            $lectures = Lecture::with(['term'])->get();

            return $lectures; 

        }
    }
	public function getMyPlan(){
        Cache::forget('queries');
		$terms = Term::with(['lecture.kind','lecture','code','teacher','hour','day','space','code'])->get();
		echo Cache::get('queries'). "<br>";
        return $terms;
        //return View::make('plan.myplan', ['lectures'=>$lectures]);
	}
	public function getPlan($user){        
        return $user;
	}
}