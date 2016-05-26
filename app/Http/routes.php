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

Route::match(['get', 'head'], '/register/{urlKey?}', [ 'middleware' => ['web', 'guest'], 'as' => 'register', 'uses' => '\App\Http\Controllers\Auth\AuthController@showRegistrationForm']);

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
	Route::get('/', function () {
		    return redirect()->route('calendar.index');

	});
    Route::get('/RO', 'ReadonlyController@index');
    Route::get('/RO/postedSchedule', 'ReadonlyController@postedSchedule');
    Route::get('/RO/test', 'ReadonlyController@modelTest');
    Route::post('/RO/schedule/drag-update', 'ReadonlyController@ajaxScheduleDragUpdate');
	Route::match(['get', 'post'], '/calendar/{calendarId}/schedule-element/{scheduleId}/user-update', ['as' => 'calendar.user-edit-schedule-element', 'uses' => 'ReadonlyController@ajaxScheduleUserUpdate']);
	Route::match(['get', 'post'], '/calendar/{calendarId}/schedule-element/user-add', ['as' => 'calendar.user-add-schedule-element', 'uses' => 'ReadonlyController@ajaxScheduleUserAdd']);
    Route::get('/calendar/schedule-element/{scheduleId}/remove', ['as' => 'calendar.user-remove-schedule-element', 'uses' => 'ReadonlyController@ajaxScheduleUserRemove']);
    Route::get('/calendar/schedule-element/{scheduleId}/tag/{tagId}/add', ['as' => 'calendar.user-tag-add', 'uses' => 'ReadonlyController@ajaxScheduleTagAdd']);
    Route::get('/calendar/schedule-element/{scheduleId}/tag/{tagId}/remove', ['as' => 'calendar.user-tag-remove', 'uses' => 'ReadonlyController@ajaxScheduleTagRemove']);
    Route::get('/calendar/{calendarId}/schedule-element/{scheduleId}/tag/edit', ['as' => 'calendar.user-tag-edit', 'uses' => 'ReadonlyController@ajaxScheduleTagEdit']);

    //Route::get('/RO/calendar/{calendarId}', 'ReadonlyController@schedule');
    Route::get('/RO/calendar/{calendarId}', ['as' => 'calendar.view', 'uses' =>'ReadonlyController@calendar']);
    //Route::get('/RO/calendar', ['as' => 'calendar.index', 'uses' => 'ReadonlyController@index']);
    Route::get('/RO/calendars', ['as' => 'calendar.index', 'uses' => 'ReadonlyController@index']);
	Route::get('/calendars', function(){

		return redirect()->route('calendar.index');
	});

    Route::get('/manage', ['as' => 'manage.index', 'uses' => 'ManageController@index']);

    Route::get('manage/calendars', ['as' => 'manage.calendar.index', 'uses' =>'ManageController@calendars']);
	Route::match(['get', 'post'], 'manage/calendars/add', ['as' => 'manage.calendar.add', 'uses' => 'ManageController@calendarsAdd']);
	Route::match(['get', 'post'], 'manage/calendars/edit/{calendarId}', ['as' => 'manage.calendar.edit', 'uses' => 'ManageController@calendarsEdit']);
    Route::get('manage/calendars/remove/{calendarId}', ['as' => 'manage.calendar.remove', 'uses' =>'ManageController@calendarsRemove']);


	
    Route::get('manage/workers', ['as' => 'manage.worker.index', 'uses' => 'ManageController@workers']);
	Route::match(['get', 'post'], 'manage/workers/add', ['as' => 'manage.worker.add', 'uses' => 'ManageController@workersAdd']);
	Route::match(['get', 'post'], 'manage/workers/edit/{workerId}', ['as' => 'manage.worker.edit', 'uses' => 'ManageController@workersEdit']);
    Route::get('manage/workers/remove/{workerId}', ['as' => 'manage.worker.remove', 'uses' =>'ManageController@workersRemove']);


    Route::get('manage/tags', ['as' => 'manage.tag.index','uses'=>'ManageController@tags']);
	Route::match(['get', 'post'], 'manage/tags/add', ['as' => 'manage.tag.add', 'uses' => 'ManageController@tagsAdd']);
	Route::match(['get', 'post'], 'manage/tags/edit/{tagId}', ['as' => 'manage.tag.edit', 'uses' => 'ManageController@tagsEdit']);
    Route::get('manage/tags/remove/{tagId}', ['as' => 'manage.tag.remove', 'uses' =>'ManageController@tagsRemove']);

	//manage calendar worker
	Route::match(['get', 'post'], 'manage/calendar/{calendarId}/workers', ['as' => 'manage.calendar-workers', 'uses' => 'ManageController@calendarWorkers']);
    Route::get('manage/calendar/{calendarId}/remove/worker/{workerId}', ['as' => 'manage.calendar-workers.remove', 'uses' =>'ManageController@calendarWorkersRemove']);
    Route::get('manage/calendar/{calendarId}/add/worker/{workerId}', ['as' => 'manage.calendar-workers.add', 'uses' =>'ManageController@calendarWorkersAdd']);

    Route::get('/manage/global-users', ['as' => 'manage.global-users', 'uses' => 'ManageController@globalUsers']);
    Route::get('/manage/calendar-invitations', ['as' => 'manage.calendar-invitations', 'uses' => 'ManageController@calendarInvitations']);
    Route::match(['get', 'post'],'/manage/calendar-invitations/add/{calendarId?}', ['as' => 'manage.calendar-invitation.add', 'uses' => 'ManageController@calendarInvitationsAdd']);
    Route::get('/manage/calendar-invitations/remove', ['as' => 'manage.calendar-invitations.remove', 'uses' => 'ManageController@calendarInvitationsRemove']);
    Route::get('/manage/calendar-invitations/remove/{invitationId}', ['as' => 'manage.calendar-invitations.remove', 'uses' => 'ManageController@calendarInvitationsRemove']);
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

