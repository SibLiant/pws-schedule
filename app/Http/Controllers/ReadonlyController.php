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


		//!Kint::dump($data); die();
		//$data['tags'] = $this->getTags();
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

}
