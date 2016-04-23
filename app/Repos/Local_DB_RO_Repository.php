<?php namespace App\Repos;

use Carbon\Carbon as Carbon;
use App\Lib\PWS_JSON_Generator;
use \DB as DB;
use \Kint as Kint;

use App\ApiCalendar;
use App\ApiWorker;
use App\ApiTag;
use App\ApiSchedule;
use App\ApiCalendarWorkerJoin;
use App\User;

class Local_DB_RO_Repository  extends PWS_DB_RO_Repository { 
	
	/**
	 *
	 * @return date string
	 */
	public function getJSON()
	{
		$raw = $this->getScheduleRecs();

		$sDate = new Carbon();	

		$sDateRange = 30;	

		$data = [
			"auth" => ["username" => "parker", "key" => "asdfifeilsdfkjlkjsdf"],
			"calendarRange" => [
				"start" => $sDate->toDateString(),
				"end" => $sDate->addDays($sDateRange)->toDateString()
			]
		];
		
		$data['scheduleRecords'] = $this->getScheduleRecs();
		$data['settings'] = [
			'navForward' => '30', 
			'navBackward' => '30',
			'navRootUrl' => ''
		];

		$data['workerRecords'] = $this->getWorkerRecs();
		$data['tags'] = $this->getTags();
		ddd($data['tags']);

		return json_encode( $data );
	}

	
	/**
	 *
	 * @return date string
	 */
	public function getTags()
	{
		$index = [];
		$tags = DB::table('tags')->get();
		foreach($tags as $tag){ 
			$index[$tag->id] = $tag;
	  	}
		return $index;
	}

	
	/**
	 *
	 * @return date string
	 */
	public function getScheduleRecs()
	{
		 $schRecs =  DB::table('projects')
			->join('customers', 'projects.customer_id', '=', 'customers.id')
			->join('schedules', 'schedules.project_id', '=', 'projects.id')
			->join('workers', 'schedules.worker_id', '=', 'workers.id')
			//->join('schedules_tags', 'schedules.id', '=', 'schedules_tags.id')
			->select(
				'workers.name as worker_name', 
				'customers.name as customer_name',
				'projects.id as project_id',
				'customers.id as customer_id',
				'workers.id as worker_id',
				'schedules.id as schedule_id',
				'schedules.scheduled_date as scheduled_date',
				'schedules.job_length_days as job_length_days',
				'schedules.schedule_note as schedule_note',
				'schedules.external_link as external_link'
			)
			->get();

		 	$schRecs = $this->indexScheduleRecsByScheduleId($schRecs);
		 	$schRecs =  $this->mergeAssignedScheduleTags($schRecs);
			return $schRecs;
	}	

	
	/**
	 *
	 *
	 * @return date string
	 */
	public function indexScheduleRecsByScheduleId($schRecs)
	{

		 $reIndex = [];

		 foreach($schRecs as $rec){ 

			$reIndex[$rec->schedule_id] = (array)$rec;

		 }
		 
		 return $reIndex;
		
	}


	
	/**
	 *
	 * @return date string
	 */
	public function mergeAssignedScheduleTags($schRecs)
	{
		$ids = $this->getExistingScheduleIdsFromSchedulesRecordSet( $schRecs );
		$tagsAssigned = $this->getAssignedTags($ids);
		$schRecs = json_decode(json_encode($schRecs), True);

		// merge tags
		foreach($tagsAssigned as $k => $v){ 
			$schRecs[$k]['tags'] = $v;
	   	}
		return $schRecs;

	}

	
	/**
	 *
	 * @return date string
	 */
	public function getExistingScheduleIdsFromSchedulesRecordSet($recordSet)
	{
		$ids = [];
		foreach( $recordSet as $k => $rec ){
			if ( ! in_array( $k, $ids ) )  array_push($ids, $k); 
		}
		return $ids;
	}

	
	/**
	 *
	 * @return date string
	 */
	public function getAssignedTags( $scheduleIds )
	{
		if ( ! is_array( $scheduleIds ) ) $scheduleIds = [$scheduleIds];
		$tags = DB::table('schedule_tag')->whereIn( 'schedule_id', $scheduleIds )->get();
		$nTags = [];
		foreach( $tags as $v ){
			$nTags[$v->schedule_id][] = $v->tag_id;

		}
		return $nTags;
	}

	
	/**
	 *
	 * @return date string
	 */
	public function getWorkerRecs()
	{
		return DB::table('workers')
			->select('workers.id as worker_id', 'workers.name as worker_name')
			->limit(5)
			->get();
	}


	
	/**
	 *
	 */
	public function build( int $calendarId, $start = null, $end = null)
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

		$data['settings'] = array_merge( $this->getApiCalendar($calendarId),   [ 'navForward' => '30', 'navBackward' => '30', 'navRootUrl' => '' ]);

		$data['workerRecords'] = $this->getApiWorkerRecs( $calendarId );

		$data['scheduleRecords'] = $this->getApiScheduleRecs($calendarId, $start, $end);
		//ddd($data['scheduleRecords']);

		$data['tags'] = $this->getApiTags($calendarId);

		return json_encode( $data );
		
	}

	
	/**
	 *
	 */
	public function getApiScheduleRecs($calendarId, $start, $end)
	{


		$recs = ApiSchedule::getRange($calendarId, $start, $end);

		$n = [];
		foreach($recs as $r){ $n[] = $r->json_data;  }

		$t = implode(',', $n);

		$jsonArr =  "[".$t."]";

		return json_decode($jsonArr);

	}

	
	/**
	 *
	 */
	public function getApiWorkerRecs($calendarId)
	{
		
		$workers = ApiCalendar::find($calendarId)->workers()->get();

		$n = [];

		foreach($workers as $c)   {

			$n[$c->id] = $c->worker_json;

		}

		return $n;

	}

	
	/**
	 *
	 */
	public function getApiTags($calendarId)
	{
		
		$Cal = ApiCalendar::find($calendarId);

		$rawTags = User::find($Cal->user_id)->tags()->get()->toArray();

		$fTags = [];
		
		foreach($rawTags as  $k => $v) $fTags[$v['id']] = $v['tag_json']; 

		return $fTags;

	}

	
	/**
	 *
	 */
	public function getApiCalendar($calendarId)
	{
		$cal = ApiCalendar::find($calendarId);

		return $cal->calendar_json;
		
	}



}
