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
use App\ApiTag;
use App\ApiWorker;
use App\ApiSchedule;
use App\Exceptions\ApiError;

class PWS_JSON_API_Controller extends Controller
{
	use \App\Http\Controllers\JWTUtils;

	public function __construct(){

		$this->middleware('jwt.auth');

	}

	
	/**
	 *
	 */
	public function calendarAdd()
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

	
	/**
	 *
	 */
	public function calendarRemove($id)
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

	
	/**
	 *
	 */
	public function calendarGet($id)
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


	
	/**
	 *
	 */
	public function workerAdd()
	{

		$workerJson = Input::get('workerJson');

		$user = $this->getAuthenticatedUser();

		if ( ! ApiWorker::isValidJson($workerJson)  ) {

			return new ApiError(500,'worker add error','worker json is not in valid form or missing required elements'  );

		}

		if ( $insertId = ApiWorker::add( $user->id, $workerJson )) {

			$respData =['data' => ['type' => 'ApiWorker', 'id' => $insertId ]];

			$Response = new JsonResponse($respData, 201);

			$Response->header('Content-Type', 'application/vnd.api+json');

			return $Response;
		}
		else {
			abort( 500, 'fail' );
		}

	}


	
	/**
	 *
	 */
	public function workerRemove($workerId)
	{

		if ( ! $workerId = intval($workerId) )
			return new ApiError(500,'worker remove error','worker id is not valid' );

		$user = $this->getAuthenticatedUser();

		if ( ! ApiWorker::remove($user->id, $workerId ) ) {
			return new ApiError(500,'schedule worker error','failed remove operation.'  );
		}

		$respData =['data' => ['type' => 'ApiWorker', 'remove' => 'succeeded' ]];

		$Response = new JsonResponse($respData, 200);

		$Response->header('Content-Type', 'application/vnd.api+json');

		return $Response;

	}

	
	/**
	 *
	 */
	public function workerGet($workerId)
	{
		
		$user = $this->getAuthenticatedUser();

		if ( ! $workerId = intval( $workerId ) )
			return new ApiError(500,'get worker error','invalid worker id'  );

		if ( $Worker = ApiWorker::get($workerId, $user->id) ) {

			$respData =['data' => json_decode( $Worker->worker_json )  ];

			$Response = new JsonResponse($respData, 200);

			$Response->header('Content-Type', 'application/vnd.api+json');

			return $Response;
			
		}

		return new ApiError(500,'worker error','failed to locate worker'  );

	}

	
	/**
	 *
	 */
	public function workerAddToCalendar($workerId, $calendarId)
	{
		if ( ! $workerId = intval($workerId) || ! $calendarId = intval($calendarId) )
			return new ApiError(500,'worker error','worker or calendar ids invalid', ['links' => ['self' => url('/').'/api/worker/']]);

		$user = $this->getAuthenticatedUser();

		$W = ApiWorker::find($workerId)->where('user_id', $user->id)->first();

		if ( ! $W )
			return new ApiError(500,'worker error','can not locate worker id', ['links' => ['self' => url('/').'/api/worker/']]);

		$C = ApiCalendar::find($calendarId)->where('user_id',$user->id)->first();

		if ( ! $C )
			return new ApiError(500,'calendar error','can not locate calendar id', ['links' => ['self' => url('/').'/api/worker/']]);

		$res = ApiWorker::addWorkerToCalendar($W->id, $C->id);

		$respData =['data' => ['type' => 'add-worker-to-calendar', 'id' => $res ]];

		$Response = new JsonResponse($respData, 201);

		$Response->header('Content-Type', 'application/vnd.api+json');

		return $Response;
		
	}

	
	/**
	 *
	 */
	public function workerRemoveFromCalendar($workerId, $calendarId)
	{

		$workerId = intval($workerId);
		$calendarId = intval($calendarId);

		if ( ! $workerId || ! $calendarId  )
			return new ApiError(500,'worker error','worker or calendar ids invalid', ['links' => ['self' => url('/').'/api/worker/']]);


		$user = $this->getAuthenticatedUser();

		$W = ApiWorker::find($workerId)->where('user_id', $user->id)->first();

		if ( ! $W )
			return new ApiError(500,'worker error','can not locate worker id', ['links' => ['self' => url('/').'/api/worker/']]);

		$C = ApiCalendar::find($calendarId)->where('user_id',$user->id)->first();

		if ( ! $C )
			return new ApiError(500,'calendar error','can not locate calendar id', ['links' => ['self' => url('/').'/api/worker/']]);

		if ( ApiWorker::removeWorkerFromCalendar($C->id, $W->id) ){

			$respData =['data' => ['type' => 'remove-worker-from-calendar', 'status' => 200 ]];

			$Response = new JsonResponse($respData, 200);

			$Response->header('Content-Type', 'application/vnd.api+json');

			return $Response;

		}

		abort(500, 'failed unlinking');
		
	}
	
	
	/**
	 *
	 */
	public function scheduleAdd()
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

	
	/**
	 *
	 */
	public function scheduleRemove($scheduleId)
	{
		
		$user = $this->getAuthenticatedUser();

		if ( ! $id = intval($scheduleId) )
			return new ApiError(500,'schedule error','schedule id is not valid', ['links' => ['self' => url('/').'/api/schedule/remove/'.$id]]);

		if ( ! ApiSchedule::apiDelete($id, $user->id) ) 
			return new ApiError(500,'schedule remove error','unable to complete remove operation.'  );

		$respData =['data' => ['type' => 'ApiSchedule', 'status' => 200 ]];

		$Response = new JsonResponse($respData, 200);

		$Response->header('Content-Type', 'application/vnd.api+json');

		return $Response;

	}
	
	/**
	 *
	 */
	public function scheduleGet($scheduleId)
	{
		
		$user = $this->getAuthenticatedUser();

		if ( ! $id = intval($scheduleId) )
			return new ApiError(500,'schedule error','schedule id is not valid', ['links' => ['self' => url('/').'/api/schedule/get/'.$id]]);

		$S =  ApiSchedule::find($id)->where('user_id', $user->id)->first();

		if ( ! $S ) 
			return new ApiError(500,'schedule error','can not find schedule id', ['links' => ['self' => url('/').'/api/schedule/get/'.$id]]);

		$respData =['data' => json_decode( $S->json_data )  ];

		$Response = new JsonResponse($respData, 200);

		$Response->header('Content-Type', 'application/vnd.api+json');

		return $Response;
		
	}

	
	/**
	 *
	 */
	public function scheduleSetInactive($scheduleId)
	{

		$user = $this->getAuthenticatedUser();

		if ( ! $id = intval($scheduleId) )
			return new ApiError(500,'schedule error','schedule id is not valid', ['links' => ['self' => url('/').'/api/schedule/remove/'.$id]]);

		if ( ! ApiSchedule::setInactive($id, $user->id) ) 
			return new ApiError(500,'schedule remove error','unable to complete remove operation.'  );

		$respData =['data' => ['type' => 'ApiSchedule', 'status' => 200 ]];

		$Response = new JsonResponse($respData, 200);

		$Response->header('Content-Type', 'application/vnd.api+json');

		return $Response;
		
	}

	
	/**
	 *
	 */
	public function scheduleUpdate()
	{

		$user = $this->getAuthenticatedUser();

		$calendarId = Input::get('calendarId');
		
		$scheduleId = Input::get('scheduleId');

		$scheduleJson = Input::get('scheduleJson');

		if ( ! ApiCalendar::userOwnsCalendar($calendarId, $user->id) ) {
			return new ApiError(500,'calender error','calendar id not found', ['links' => ['self' => url('/').'/api/schedule/add']]);
		}

		if ( ! ApiSchedule::isValidScheduleJson( $scheduleJson )  ) {
			return new ApiError(500,'schedule add error','schedule json is not in valid form or missing required elements'  );
		}

		

		if ( $insertId = ApiSchedule::ApiUpdate($calendarId, $scheduleId, $user->id, $scheduleJson)  ) {

			$respData =['data' => ['type' => 'ApiSchedule', 'id' => $insertId ]];

			$Response = new JsonResponse($respData, 201);

			$Response->header('Content-Type', 'application/vnd.api+json');

			return $Response;
			
		}

		return new ApiError(500,'schedule update error','unable to locate existing record for update'  );

		
	}

	
	/**
	 *
	 */
	public function tagAdd()
	{

		$user = $this->getAuthenticatedUser();

		$tagJson = Input::get('tagJson');

		if ( ! ApiTag::isValidJson( $tagJson )  ) {
			return new ApiError(500,'tag add error','tag json is not in valid form or missing required elements'  );
		}


		if ($insertId = ApiTag::add($user->id, $tagJson)  ){

			$respData =['data' => ['type' => 'ApiSchedule', 'id' => $insertId ]];

			$Response = new JsonResponse($respData, 201);

			$Response->header('Content-Type', 'application/vnd.api+json');

			return $Response;

		}

		abort(500, 'unable to add tag');
		
	}

	
	/**
	 *
	 */
	public function tagRemove($tagId)
	{

		$user = $this->getAuthenticatedUser();

		if ( ! $id = intval($tagId) )
			return new ApiError(500,'tag error','tag id is not valid', ['links' => ['self' => url('/').'/api/tag/remove/'.$id]]);

		if ( ! ApiSchedule::apiDelete($id, $user->id) ) 
			return new ApiError(500,'tag remove error','unable to complete remove operation.'  );


		$respData =['data' => ['type' => 'ApiTag', 'status' => 200 ]];

		$Response = new JsonResponse($respData, 200);

		$Response->header('Content-Type', 'application/vnd.api+json');

		return $Response;
		
	}

	
	/**
	 *
	 */
	public function tagGet($tagId)
	{

		$user = $this->getAuthenticatedUser();

		if ( ! $id = intval($tagId) )
			return new ApiError(500,'tag error','schedule id is not valid', ['links' => ['self' => url('/').'/api/tag/get/'.$id]]);

		$S =  ApiTag::find($id)->where('user_id', $user->id)->first();

		if ( ! $S ) 
			return new ApiError(500,'tag error','can not find schedule id', ['links' => ['self' => url('/').'/api/tag/get/'.$id]]);

		$respData =['data' => json_decode( $S->tag_json )  ];

		$Response = new JsonResponse($respData, 200);

		$Response->header('Content-Type', 'application/vnd.api+json');

		return $Response;
		
	}


}
