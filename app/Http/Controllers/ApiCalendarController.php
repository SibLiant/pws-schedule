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
use App\ApiCalendar;
use App\Exceptions\ApiError;


class ApiCalendarController extends Controller
{
	
	use \App\Http\Controllers\JWTUtils;

	public function __construct(){

		$this->middleware('jwt.auth');

	}

    public function add()
    {
		$user = $this->getAuthenticatedUser();

		$calendarJson = Input::get('calendarJson');

		if ( ! ApiCalendar::isValidJson( $calendarJson )  ) {
			return new ApiError(500,'calendar add error','calendar json is not in valid form or missing required elements'  );
		}


		if ( ! $insertId = ApiCalendar::add( $user->id, $calendarJson) )
			return new ApiError(500,'calender error','error adding calendar item', ['links' => ['self' => url('/').'/api/calendar/add']]);

		$respData =['data' => ['type' => 'ApiCalendar', 'id' => $insertId ]];

		$Response = new JsonResponse($respData, 201);

		$Response->header('Content-Type', 'application/vnd.api+json');

		return $Response;

    }

    public function remove($id)
    {

		if ( ! $id = intval($id) )
			return new ApiError(500,'calender error','calendar id not valid', ['links' => ['self' => url('/').'/api/calendar/remove']]);

		$user = $this->getAuthenticatedUser();

		if ( ! ApiCalendar::remove($id, $user->id) ) 
			return new ApiError(500,'calender error','failed to remove calendar id', ['links' => ['self' => url('/').'/api/calendar/remove']]);

		$respData =['data' => ['type' => 'ApiCalendar', 'remove' => 'success' ]];

		$Response = new JsonResponse($respData, 200);

		$Response->header('Content-Type', 'application/vnd.api+json');

		return $Response;

    }

	public function get($id)
	{


		if ( ! $id = intval($id) )
			return new ApiError(500,'calender error','calendar id not valid', ['links' => ['self' => url('/').'/api/calendar/remove']]);

		$user = $this->getAuthenticatedUser();

		if ( ! $Cal = ApiCalendar::get($id, $user->id)  )
			return new ApiError(500,'calender error','calendar id not found', ['links' => ['self' => url('/').'/api/calendar/get']]);

		if ( $Cal = ApiCalendar::get($id, $user->id) ) {

			$respData =['data' => json_decode( $Cal->calendar_json )  ];

			$Response = new JsonResponse($respData, 200);

			$Response->header('Content-Type', 'application/vnd.api+json');

			return $Response;
			
		}

		return new ApiError(500,'calendar error','failed to locate calendar'  );
		

	}



}
