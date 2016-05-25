<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repos;
use App\Lib\JsonValidator;
use App\Lib\PWS_JSON_Generator;
use Illuminate\Support\Facades\Input;
use App\Exceptions\Handler;
use App\Calendar;
use App\ApiCalendar;
use App\ApiSchedule;
use App\ApiScheduleElement;
use Kint;
use Carbon\Carbon;
use Auth;
use Gate;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

class ReadonlyController extends Controller
{
		Protected $repo;

    //
	
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(\App\Repos\Local_DB_RO_Repository $repo)
    {
		$this->middleware('auth');

		$this->repo = $repo;
    }

	/**
	* undocumented class
	*
	* @package default
	* @subpackage default
	* @author Parker Bradtmiller
	*/
    public function index(Request $request)
    {

		//get all the calendars this user is invited too
		$inv = \App\CalendarInvitation::where('email', Auth::user()->email)->pluck('calendar_id')->toArray();

		//get all the calendar ids the user actually owns
		$userCalIds = ApiCalendar::where('user_id', Auth::user()->id)->pluck('id')->toArray();

		//combine all cal ids
		$cals = array_merge($inv,$userCalIds);

		$cals = ApiCalendar::whereIn('id', $cals)->get();

		//d($inv);
		//ddd($cals);

		$schRecCounts[] = [];
		foreach($cals as $c){ 
			$schRecCounts[$c->id] = ApiSchedule::where('calendar_id', $c->id)->count();
	   	}



		return view('calendars.index', [
				'cals' => $cals ,
				'schRecCounts' => $schRecCounts 
			]
		);

    }

	/**
	 *
	 * @return date string
	 */
	public function postedSchedule(Request $request)
	{

		$jsonGen = new PWS_JSON_Generator();

		$post = \DB::table('api_full_posts')
			//->where('user_id', '=', $user->id)
			->where('user_id', '=', 1)
			->orderBy('id', 'desc')
			->limit(1)
			->get();

		$json = $jsonGen->getJSON();

		if ( $post[0] ) {
			
			return view('ro_page')->with('json_data', $post[0]->json);

		}
		else {

			Session::flash('msg-error', 'no json post data found');

			return view('/ro_page');
		}
		
	}

	
	/**
	 *
	 * @return date string
	 */
	public function modelTest()
	{
		$res = ApiScheduleElement::getRange(1, '2016-04-01', '2016-04-15');
		$res = ApiScheduleElement::buildScheduledArray( $res );

		ddd($res);

		
	}

	
	/**
	 *
	 * @return date string
	 */
	public function schedule($calendarId)
	{

		if ( ! $calendarId = intval($calendarId) ) abort(500, "inappropriate calendar!");

		$carbonNow = new Carbon();

		$sDate = new Carbon();	

		$sDateRange = 30;	

		$data = [
			"auth" => ["username" => "parker", "key" => "asdfifeilsdfkjlkjsdf"],
			"calendarRange" => [
				"start" => $sDate->toDateString(),
				"end" => $sDate->copy()->addDays($sDateRange)->toDateString()
			]
		];
		
		$data['settings'] = [
			'navForward' => '30', 
			'navBackward' => '30',
			'navRootUrl' => ''
		];

		$data['scheduleRecords'] = ApiScheduleElement::buildScheduledArray( 
			ApiScheduleElement::getRange($calendarId, $sDate->toDateString() 
		));

		$data['workerRecords'][] = ['worker_id'=>1, 'worker_name'=>'peters' ];
		$data['workerRecords'][] = ['worker_id'=>2, 'worker_name'=>'justin' ];
		$data['workerRecords'][] = ['worker_id'=>3, 'worker_name'=>'williams' ];


		$data['tags'] = [];


		//$json = $this->repo->getJSON();

		//if (  JsonValidator::isValid($json)){

		return view('ro_page')->with('json_data', json_encode( $data  ));

		//}

		//abort(500, 'invalid json');
		
	}

	
	/**
	 *
	 */
	public function calendar($calendarId)
	{

		$cal = \App\ApiCalendar::find($calendarId);

		if ( Gate::denies('calendar-view', $cal) ) abort(403);

		$json = $this->repo->build($calendarId);

		if (  JsonValidator::isValid($json)){

        	return view('ro_page')->with('json_data', $json);

		}

		abort(500, 'invalid json');
	}

	
	/**
	 *
	 */
	public function ajaxScheduleUserUpdate(Request $request, $calendarId, $scheduleId)
	{

		if ($request->ajax() && $request->isMethod("post"))
		{
			$validation= [
				'scheduled_date' => 'required|date_format:"Y-m-d"',
				'customer_name' => 'required'
			];

			$this->validate($request, $validation);

			
			if ( ! $newRec = $this->repo->saveUserScheduleUpdate(Input::all()) ){

				abort(422);

			}

			$respData = json_decode( $newRec->json_data );

			return new JsonResponse($respData, 200);


		}
		
		return view('user_update')->with('scheduleData', $this->repo->getUserScheduleUpdateData($scheduleId));
		
	}

	/**
	 *
	 */
	public function ajaxScheduleTagRemove($scheduleId, $tagId)
	{
		
		if ( $newRec = $this->repo->removeTagFromScheduelElement($scheduleId, $tagId)  ){

			$respData = json_decode( $newRec->json_data );

			return new JsonResponse($respData, 200);

		}

		return abort(422);
		
	}

	/**
	 *
	 */
	public function ajaxScheduleTagAdd($scheduleId, $tagId)
	{

		if ( $newRec = $this->repo->addTagToScheduelElement($scheduleId, $tagId)  ){

			$respData = json_decode( $newRec->json_data );

			return new JsonResponse($respData, 200);

		}

		return abort(422);
		
	}

	/**
	 *
	 */
	public function ajaxScheduleTagEdit($calendarId, $scheduleId)
	{

		$tagData = $this->repo->getEditTagsData($calendarId, $scheduleId);

		return view('user_tag_edit')->with('tagData', $tagData );
		
	}


	/**
	 *
	 */
	public function ajaxScheduleUserRemove($scheduleId)
	{

		//gate
		
		if ( ApiSchedule::setInactive($scheduleId, Auth::user()->id) ){

			return new JsonResponse(["response"=>"success"], 200);

		}

		return new JsonResponse(["response"=>"error: unable to update record"], 422);
		
	}

	
	/**
	 *
	 */
	public function ajaxScheduleUserAdd(Request $request, $calendarId)
	{

		if ($request->ajax() && $request->isMethod("post"))
		{
			$validation= [
				'scheduled_date' => 'required|date_format:"Y-m-d"',
				'customer_name' => 'required',
				'project_id' => 'required',
				'worker_id' => 'required'
			];

			$this->validate($request, $validation);

			
			if ( ! $newRec = $this->repo->saveUserScheduleAdd(Input::all(), $calendarId) ){

				abort(422);

			}

			$respData = json_decode( $newRec->json_data );

			return new JsonResponse($respData, 200);
		}

		$data = $this->repo->getUserScheduleAddData($calendarId);

		$data['calendar_id'] = $calendarId;

		return view('user_add')->with('scheduleData', $data );

		
	}

	
	/**
	 *
	 */
	public function ajaxScheduleDragUpdate(Request $request)
	{

		$targetRec = Input::get('targetRecord');

		$fieldsToUpdate = Input::get('updateFields');

		$Schedule = new ApiSchedule();

		$newRec = $Schedule->updateRec($targetRec, $fieldsToUpdate, Auth::user()->id );
		
		$respData = json_decode( $newRec->json_data );

		return new JsonResponse($respData, 200);
		
	}

}
