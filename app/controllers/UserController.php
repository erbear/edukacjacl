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
        if (Crypt::decrypt($user->password) == Input::get('password')){
            Auth::loginUsingId($user->id);
            return Auth::user()->login;

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
        $user->password = Crypt::encrypt(Input::get('password'));
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
