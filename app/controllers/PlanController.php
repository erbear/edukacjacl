<?php
class PlanController extends BaseController
{
	public function getAllTerminy()
	{
        //potrzebuje zalogowanego użytkownika
        $user = Auth::user();

        $lecture_id = array();

        //najpierw chce pobrać z bazy wszyskie zajecia, które nalezą do danego kierunku
        foreach($user->fields as $field)
        {
            foreach ($field->terms as $term)
            {                
                array_push($lecture_id, $term->lecture_id);                
            }
        }

        //jeśli takich zajęć nie ma to łącze się z edukacją
        if(empty($lecture_id))
        {
            $edukacja = new EdukacjaCl($user->login, Crypt::decrypt($user->password));
            $courses = $edukacja->pobierzKursyZWektora();

            $adder = new AddRecords($courses);

            $records = $adder->addTerms($edukacja->getOpisStudiow()['semestr']);
            if($records != null)
            {
                foreach($user->fields as $field)
                {
                    $adder->addFieldTerm($field, $records); 

                }
            }

            return Redirect::to('/plan');
        }
        $lectures = Lecture::with('kind','terms','terms.code','terms.teacher', 'terms.hour','terms.day',
                                'terms.space','terms.code')->whereIn('id', $lecture_id)->get();

        return $lectures;
	}


    public function getIndex()
    {
        //potrzebuje zalogowanego użytkownika
        $user = Auth::user();

        $lecture_id = array();

        //najpierw chce pobrać z bazy wszyskie zajecia, które nalezą do danego kierunku
        foreach($user->fields as $field)
        {
            foreach ($field->terms as $term)
            {                
                array_push($lecture_id, $term->lecture_id);                
            }
        }

        //jeśli takich zajęć nie ma to łącze się z edukacją
        if(empty($lecture_id))
        {
            $edukacja = new EdukacjaCl($user->login, Crypt::decrypt($user->password));
            $courses = $edukacja->pobierzKursyZWektora();

            $adder = new AddRecords($courses);

            $records = $adder->addTerms($edukacja->getOpisStudiow()['semestr']);
            if($records != null)
            {
                foreach($user->fields as $field)
                {
                    $adder->addFieldTerm($field, $records); 

                }
            }

            return Redirect::to('/plan');
        }
        $lectures = Lecture::with('kind','terms','terms.code','terms.teacher', 'terms.hour','terms.day',
                                'terms.space','terms.code')->whereIn('id', $lecture_id)->get();

        return View::make('plan.index', array('plan'=> $lectures));
    }
    public function postZapiszPlan(){
        $wszystkie = array();//tutaj beda wszystkie kursy
        foreach (Input::get() as $input){
            foreach ($input as $i){
                $wszystkie[] = $i;
            }
        }
        $kod = $wszystkie[0]['code']['name'];
        return $wszystkie;//tutaj maja byc pobrene z bazy terminy na ktore mnie zapisales, yo!
    }

}