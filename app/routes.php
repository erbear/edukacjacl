<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Event::listen('illuminate.query',function($sql, $bindings, $time, $name){
	if (Cache::has('queries'))
	{
		$queries = Cache::get('queries');
		$queries++;
		Cache::put('queries', $queries, 100);
	}
	else
	{
		Cache::add('queries', 0, 100);
	}
{
    //
}
});

Route::controller('/user', 'UserController');
Route::controller('/plan', 'PlanController');

Route::get('day', function()
{
	$day = Lecture::find(2)->day()->get();
	return View::make('daysGetForm')->with('day', $day);
});

Route::post('day', function()
{
	
});


Route::get('login/fb', function() {
    $facebook = new Facebook(Config::get('facebook'));
    $params = array(
        'redirect_uri' => url('/login/callback'),
    );
    return Redirect::to($facebook->getLoginUrl($params));
});

Route::get('login/callback', function() {
    $code = Input::get('code');
    echo ('code');
    if (strlen($code) == 0) return Redirect::to('/')->with('message', 'There was an error communicating with Facebook');
 
    $facebook = new Facebook(Config::get('facebook'));
    $uid = $facebook->getUser();
 
    if ($uid == 0) return Redirect::to('/')->with('message', 'There was an error');
 
    $me = $facebook->api('/me');
 
    $profile = Profile::whereUid($uid)->first();
    if (empty($profile)) {
 
        $user = new User;
        $user->login = 'pwr179510';
        $user->password = Crypt::encrypt('Ll35p@kK'); 
        $user->save();
 
        $profile = new Profile();
        $profile->uid = $uid;
        $profile->username = 'pwr179510';
        $profile = $user->profiles()->save($profile);
    }
 
    $profile->access_token = $facebook->getAccessToken();
    $profile->save();
 
    $user = $profile->user;
 
    Auth::login($user);
 
    echo 'Logged in with Facebook';
});

Route::get('login/callback', function() {
    $code = Input::get('code');
    dd($code);
    echo "<br> <br>";
    if (strlen($code) == 0) return Redirect::to('/')->with('message', 'There was an error communicating with Facebook');
 
    $facebook = new Facebook(Config::get('facebook'));
    $uid = $facebook->getUser();
 
    if ($uid == 0) return Redirect::to('/')->with('message', 'There was an error');
 
    $me = $facebook->api('/me/friends');
 
    dd($me);
});

Route::get('logout', function()
{
	$facebook = new Facebook(Config::get('facebook'));
	return Redirect::to($facebook->getLogoutUrl());
});
