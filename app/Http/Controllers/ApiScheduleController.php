<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\JWTUtils;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\User;
use App\Lib\JsonValidator;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\JsonResponse;
use \Kint as Kint;
use App\ApiSchedule;
use App\ApiCalendar;
use App\Exceptions\ApiError;

class ApiScheduleController extends Controller
{

	use \App\Http\Controllers\JWTUtils;

	public function __construct(){

		$this->middleware('jwt.auth');

	}

    public function add()
    {

		$user = $this->getAuthenticatedUser();

		$calendarId = Input::get('calendarId');

		$scheduleJson = Input::get('scheduleJson');

		if ( ! ApiCalendar::userOwnsCalendar($calendarId, $user->id) ) {
			return new ApiError(500,'calender error','calendar id not found', ['links' => ['self' => url('/').'/api/schedule/add']]);
		}

		if ( ! ApiSchedule::isValidScheduleJson( $scheduleJson )  ) {
			return new ApiError(500,'schedule add error','schedule json is not in valid form or missing required elements'  );
		}

		$insertId = ApiSchedule::add($calendarId, $user->id, $scheduleJson);

		$respData =['data' => ['type' => 'ApiSchedule', 'id' => $insertId ]];

		$Response = new JsonResponse($respData, 201);

		$Response->header('Content-Type', 'application/vnd.api+json');

		return $Response;

    }


    public function remove($id)
    {

		$user = $this->getAuthenticatedUser();

		if ( ! $id = intval($id) )
			return new ApiError(500,'schedule error','schedule id is not valid', ['links' => ['self' => url('/').'/api/schedule/remove/'.$id]]);

		if ( ! ApiSchedule::setInactive($id, $user->id) ) {
			return new ApiError(500,'schedule remove error','unable to complete remove operation.'  );
		}

		$respData =['data' => ['type' => 'ApiSchedule', 'remove' => 'succeeded' ]];

		$Response = new JsonResponse($respData, 200);

		$Response->header('Content-Type', 'application/vnd.api+json');

		return $Response;

    }

	
	/**
	 *
	 * @return date string
	 */
	public function get($id)
	{

		$user = $this->getAuthenticatedUser();

		if ( ! $id = intval($id) )
			return new ApiError(500,'schedule error','schedule id is not valid', ['links' => ['self' => url('/').'/api/schedule/remove/'.$id]]);


		
	}

}
