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
            $term_field = DB::table('field_term')->where('field_id', '=', $field->id)
                                        ->where('semestr', '=', 3)
                                        ->where('year', '=', date("Y"))->get();
            if($term_field == null) break;
            foreach($term_field as $t)
            {
                $term_id[] = $t->term_id;
            }
            
            $terms = DB::table('terms')->whereIn('id', $term_id)->get();
            foreach ($terms as $term)
            {                
                array_push($lecture_id, $term->lecture_id);                
            }
        }

        //jeśli takich zajęć nie ma to łącze się z edukacją
        if(empty($lecture_id))
        {
            $edukacja = new EdukacjaCl($user->login, Crypt::decrypt($user->password));
            $edukacja->logIn();
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
            $term_field = DB::table('field_term')->where('field_id', '=', $field->id)
                                        ->where('semestr', '=', 3)
                                        ->where('year', '=', date("Y"))->get();
            if($term_field == null) break;
            foreach($term_field as $t)
            {
                $term_id[] = $t->term_id;
            }
            
            $terms = DB::table('terms')->whereIn('id', $term_id)->get();
            foreach ($terms as $term)
            {                
                array_push($lecture_id, $term->lecture_id);                
            }
        }

        //jeśli takich zajęć nie ma to łącze się z edukacją
        if(empty($lecture_id))
        {
            $edukacja = new EdukacjaCl($user->login, Crypt::decrypt($user->password));
            $edukacja->logIn();
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
                $wszystkie[] = $i['code']['name'];
            }
        }        
        $kod = $wszystkie[0];
        return $wszystkie;//tutaj maja byc pobrene z bazy terminy na ktore mnie zapisales, yo!
    }

}

