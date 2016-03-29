<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


Route::get('/', function () { //return view('welcome');
	return view('pws_scheduler_home');
});

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {

    Route::auth();
    Route::get('/home', 'HomeController@index');
    Route::get('/RO', 'ReadonlyController@index');
    Route::get('/RO/postedSchedule', 'ReadonlyController@postedSchedule');
});


Route::group(['prefix' => 'api'], function(){
	Route::post('authenticate', 'JWTAuthController@authenticate');
	Route::post('addLastPostJson', 'JWTAuthController@addLastPostJson');
	Route::post('postJSON', 'JWTAuthController@postJSON');
});





//Route::get('/api/clientJSON', [ 'before' => 'jwt-auth', function () {
   //}]);
