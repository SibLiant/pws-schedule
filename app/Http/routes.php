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
    Route::get('/RO/test', 'ReadonlyController@modelTest');
    //Route::get('/RO/calendar/{calendarId}', 'ReadonlyController@schedule');
    Route::get('/RO/calendar/{calendarId}', 'ReadonlyController@calendar');

});



Route::group(['prefix' => 'api'], function(){

	Route::post('authenticate', 'JWTAuthController@authenticate');
	Route::post('addLastPostJson', 'JWTAuthController@addLastPostJson');
	Route::post('postJSON', 'JWTAuthController@postJSON');



	//calendar
	Route::post('calendar/add', 'PWS_JSON_API_Controller@calendarAdd');
	Route::get('calendar/remove/{calendarId}', 'PWS_JSON_API_Controller@calendarRemove');
	Route::get('calendar/get/{calendarId}', 'PWS_JSON_API_Controller@calendarGet');


	//worker
	Route::post('worker/add', 'PWS_JSON_API_Controller@workerAdd');
	Route::get('worker/remove/{workerId}', 'PWS_JSON_API_Controller@workerRemove');
	Route::get('worker/get/{workerId}', 'PWS_JSON_API_Controller@workerGet');
	Route::get('worker/{workerId}/add-to-cal/{calendarId}', 'PWS_JSON_API_Controller@workerAddToCalendar');
	Route::get('worker/{workerId}/remove-from-cal/{calendarId}', 'PWS_JSON_API_Controller@workerRemoveFromCalendar');


	//Route::post('add_schedule_element', 'PWS_JSON_API_Controller@addScheduleElement');
	Route::post('schedule/add', 'PWS_JSON_API_Controller@scheduleAdd');
	Route::get('schedule/remove/{scheduleId}', 'PWS_JSON_API_Controller@scheduleRemove');
	Route::get('schedule/get/{scheduleId}', 'PWS_JSON_API_Controller@scheduleGet');
	Route::get('schedule/setInactive/{scheduleId}', 'PWS_JSON_API_Controller@scheduleSetInactive');
	Route::post('schedule/update', 'PWS_JSON_API_Controller@scheduleUpdate');

	//tags
	Route::post('tag/add', 'PWS_JSON_API_Controller@tagAdd');
	Route::get('tag/remove/{tagId}', 'PWS_JSON_API_Controller@tagRemove');
	Route::get('tag/get/{tagId}', 'PWS_JSON_API_Controller@tagGet');

});





//Route::get('/api/clientJSON', [ 'before' => 'jwt-auth', function () {
   //}]);
