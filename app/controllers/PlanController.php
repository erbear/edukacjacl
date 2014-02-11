<?php
class PlanController extends BaseController
{
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

            foreach($user->fields as $field)
            {
                foreach ($field->terms as $term)
                {                
                    array_push($lecture_id, $term->lecture_id);                
                }
            }
        }
        $lectures = Lecture::with('kind','terms','terms.code','terms.teacher', 'terms.hour','terms.day',
                                'terms.space','terms.code')->whereIn('id', $lecture_id)->get();

        return $lectures;
	}


    public function getPost()
    {
        
    }

}