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

        //print_r($courses);
        Cache::forget('queries');
        $adder = new AddRecords($courses);
        $adder->addOne("days", $adder->c_days);
        $adder->addOne("kinds", $adder->c_kinds);
        $adder->addOne("teachers", $adder->c_teachers);
        $adder->addOne("codes", $adder->c_codes);

        $adder->addTwo('hours', $adder->c_hours, array("start", "finish"));
        $adder->addTwo('places', $adder->c_places, array("building", "room"));
        $adder->addTwo('spaces', $adder->c_spaces, array("taken", "all"));

        //foreach ($courses as $key => $course) {

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



        //}

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
		$lectures = Lecture::with('kind','terms','terms.code','terms.teacher',
                                    'terms.hour','terms.day','terms.space','terms.code')->get();
		echo Cache::get('queries');
        echo '<br>';
        return $lectures;
        //return View::make('plan.myplan', ['lectures'=>$lectures]);
	}
	public function getPlan($user){        
        return $user;
	}

    public function getDay()
    {
        Cache::forget('queries');

        $days = array('cz', 'pt', 'wt');
       // $days2 = DB::table('days')->insert(array('name' => 'czw'));
        $day = DB::table('days')->whereIn('name', array('czw', 'pt'))->get();

        print_r($day);

        echo Cache::get('queries');
        echo '<br>';

        //return $days2;
    }
}