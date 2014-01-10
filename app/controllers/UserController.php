<?php
class UserController extends BaseController
{
	public function getLogin()
    {
            return View::make('user.login');
    }

    public function postLogin()
    {
        $user = User::where('login', Input::get('login'))->first();
        if (!$user){
            $edukacja = new EdukacjaCl(Input::get('login'), Input::get('password'));
            $edukacja->logIn();
            if ($edukacja->getDane()['uzytkownik'] != null){
                $user = new User;
                $user->login = Input::get('login');
                $user->password = Crypt::encrypt(Input::get('password'));
                $user->save();
                Auth::loginUsingId($user->id);
                return 'zostales zalogowany1';
            }else {
                return 'nie ma takiego konta na edukcji1';
            }
        }else {
            if (Crypt::decrypt($user->password) == Input::get('password')){
                Auth::loginUsingId($user->id);
                return 'zostales zalogowany2';
            }else {
                return 'wpisales cos nie tak2';
            }
        }
    }
}
