<?php
class UserController extends BaseController
{

	public function getIndex()
    {
            //widok zawiera formularz z login i password oraz przycisk zaloguj
            return View::make('user.login');
    }

    public function postIndex()
    {        

        //szukm użytkownika w bazie
        $user = User::where('login', Input::get('login'))->first(); 
        //jeśli go nie znajde w bazie
        if (empty($user))
        {
            //loguje sie do edukacji
            $edukacja = new EdukacjaCl(Input::get('login'), Input::get('password'));
            $daneArray = $edukacja->getOpisStudiow();
            $field = $daneArray['ciag'];
            $dane = $edukacja->getDane();
            //jesli uda się zalogować, tworze nowego użytkownika                
            if ($dane['uzytkownik'] != null){
                $user = new User;
                $user->login = Input::get('login');
                $user->password = Crypt::encrypt(Input::get('password'));
                $user->save();
                Auth::loginUsingId($user->id);

                $adder = new AddRecords();
                $adder->addFieldUser($field, $user);
                //return Redirect::to('/user/register-fb');            
            }
            else 
            {
                return 'nie ma takiego konta na edukcji1'. $dane['uzytkownik'];                
            }
        }
            
        //użytkownik jest w bazie        
        else
        {
            //sprawdzam poprawność hasła
            if(Crypt::decrypt($user->password) == Input::get('password'))
            {                
                Auth::loginUsingId($user->id);
                // $profile = Profile::where('user_id', $user->id)->first();
                // if (empty($profile))
                // {
                //     return Redirect::to('/user/register-fb');
                // }
                // $facebook = new Facebook(Config::get('facebook'));
                // $params = array('redirect_uri' => url('/user/login-fb'));
                // return Redirect::to($facebook->getLoginUrl($params));
            }
            //jeśli jest błędne hasło
            else 
            {
                return 'wpisales cos nie tak2';
            }
        }   

        return Redirect::to('/plan/');     
        
    }

    //wylogowywanie się
    public function getLogout()
    {
        //usune sesje z facebook'iem, przy logowaniu muszę połączyć się od nowa zawsze
        //$facebook = new Facebook(Config::get('facebook'));
        //$facebook->destroySession();
        Auth::logout();
        return Redirect::to('/user/login');
    }

    // //łączenie konta z facebook'iem
    // public function getRegisterFb()
    // {
    //     echo View::make('user.register-fb');
    // }
    // //jeśli już ktoś chce połączyć swoje konto z facebook'iem
    // public function postRegisterFb()
    // {
    //     $facebook = new Facebook(Config::get('facebook'));
    //     $params = array('redirect_uri' => url('/user/login-fb'));
    //     return Redirect::to($facebook->getLoginUrl($params));
    // }
    // //po zalogowaniu do facebook'a
    // public function getLoginFb()
    // {
    //     //sprawdzam, czy dostałem coś spowrotem(coś muszę dostać)
    //     $code = Input::get('code');
    //     if (strlen($code) == 0) return 'Problem z połączeniem z facebook\'iem';
        
    //     //pobieram zalogowanego użytkownika
    //     $facebook = new Facebook(Config::get('facebook'));
    //     $uid = $facebook->getUser();
    //     //jeśli jest 0 to nie ma zalogowanego użytkownika
    //     if ($uid == 0) return 'Problem z użytkowniem';
        
    //     //całe info o użytkowniku z fejsa
    //     $me = $facebook->api('/me');

    //     //użytkownik jest już zalogowany
    //     $user = Auth::user();
        
    //     $profile = Profile::where('user_id', $user->id)->first();
    //     if (empty($profile))
    //     {
    //         $profile = new Profile();
    //         $profile->uid = $uid;
    //         $profile->username = $me['name'];
    //         $profile->user()->associate($user);
    //     }
    //     //tworze nowy profil       
    //     $profile->access_token = $facebook->getAccessToken();
    //     $profile->save();
    //     return 'Połączono konto z facebook\'iem';
    // } 
    
}

