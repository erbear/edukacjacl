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

