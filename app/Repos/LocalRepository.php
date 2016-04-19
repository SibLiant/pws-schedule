<?php namespace App\Repos;

use Carbon\Carbon as Carbon;
use \DB as DB;
use \Kint as Kint;

use App\ApiCalendar;
use App\ApiWorker;
use App\ApiTag;
use App\ApiSchedule;
use App\ApiCalendarWorkerJoin;
use App\User;

class LocalRepository  extends PWS_DB_RO_Repository { 
	
	/**
	 *
	 */
	public function build($calendarId, $start = null, $end = null)
	{


		//get our dates into carbon formats for easy and secure use
		if ( ! $start ) $start = new Carbon();

		if ( $start && ! ( $start instanceof Carbon ) ) {

			$start = new Carbon($start);

		} 		

		if ( ! $end ) $end = $start->copy()->addDays(30);

		if ( $end && ! ($start instanceof Carbon) ) {

			$end = new Carbon($end);
		} 

		//build our baisc calendar settings
		//todo: a helper here that ensures defaults and validity
		$data['calendarRange'] = [ "start" => $start->toDateString(), "end" => $end->toDateString() ];

		$data['settings'] = [ 'navForward' => '30', 'navBackward' => '30', 'navRootUrl' => '' ];

		$data['workerRecords'] = $this->getApiWorkerRecs( $calendarId );

		$data['scheduleRecords'] = $this->getApiScheduleRecs($calendarId, $start, $end);

		$data['tags'] = $this->getApiTags($calendarId);

		return json_encode( $data );
		
	}

	
	/**
	 *
	 */
	public function getApiScheduleRecs($calendarId, $start, $end)
	{


		$r = ApiSchedule::getRange($calendarId, $start, $end);

		return ApiSchedule::jsonFieldsToArray($r, 'json_data');

	}

	
	/**
	 *
	 */
	public function getApiWorkerRecs($calendarId)
	{
		
		$workers = ApiCalendar::find($calendarId)->workers()->get();

		return ApiWorker::jsonFieldsToArray($workers, 'worker_json');

	}

	
	/**
	 *
	 */
	public function getApiTags($calendarId)
	{
		
		$Cal = ApiCalendar::find($calendarId);

		$rawTags = User::find($Cal->user_id)->tags()->get()->toArray();

		$fTags = [];
		
		foreach($rawTags as  $k => $v) $fTags[$v['id']] = json_decode($v['tag_json']); 

		return $fTags;

	}



}
