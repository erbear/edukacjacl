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
	echo $time . '<br>';
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
