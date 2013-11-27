<?php
class UserController extends BaseController
{
	public function getLogin()
    {
            return View::make('user.login');
    }

    public function postLogin()
    {
        $credentials = array(
                        'login'=> Input::get('login'),
                        'password'=> Input::get('password'),
                );
        
        if (Auth::attempt($credentials))
        {
        return View::make('message')->with('message', 'Zalogowano!');
        }else
        {
        return View::make('message')->with('message', 'Logowanie nie powiodło się :(');
        }
    }

	public function getRegister()
    {
        return View::make('user.register');
    }
    public function postRegister()
    {
        $user = new User;
        $user->login = Input::get('login');
        $user->password = Hash::make(Input::get('password'));
        $user = $user->save();
        if ($user)
        {
                return View::make('message')->with('message', 'zostales zarejestrowany');        
        }else
        {
                return View::make('message')->with('message', 'wystapil blad, sprobuj jeszcze raz');
        }
    }
}
