<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \Kint;
use Carbon\Carbon;
use \DB;

class ApiSchedule extends ApiModel
{
	
	use SoftDeletes;

	public $timestamps = true;

	protected $guarded = ['id', 'created_at', 'updated_at'];
	
	//protected $casts = [ 'json_data' => 'array' ];

	public $jsonDefaults = [
		'job_length_days' => '',
		'scheduled_date' => ''
	];

	
	/**
	 *
	 * @return date string
	 */
	public function findByCalendarId($calendarId,  $active = true)
	{

		
		$sql = 'select * from api_schedules ';
		if ( $active  ) {
			$where = 'where calendar_id = ? and active = true order by id desc';
		} else {

			$where = 'where calendar_id = ? order by id desc';
		}

		$sql = $sql . $where;

		return \DB::select($sql, [$calendarId]);
	}

	

	/**
	 *
	 * @return date string
	 */
	//public function findByWorkerId($calendarId, $workerId)
	public function findByWorkerId($calendarId, $workerId)
	{
		
		if ( ! is_int($calendarId) || ! is_int($workerId) ) return false;

		return DB::table('api_schedules')
			->whereRaw("json_data->'worker_id' = '{$workerId}' and calendar_id = {$calendarId}")
			->get();
	}

	/**
	 *
	 * @return date string
	 */
	public static function add($calendarId, $userId, $json, $parentId = null)
	{

		//set the schedule id to ensure it's unique
		$schArr = json_decode($json);

		$newId = self::nextVal('api_schedules_id_seq');

		//if ( ! isset($schArr->schedule_id) )
		$schArr->schedule_id = $newId;

		$end = new Carbon($schArr->scheduled_date);

		$end = $end->addDays($schArr->job_length_days)->toDateString();

		$schArr->job_end_date = $end;

		$json = json_encode($schArr);

		return \DB::table('api_schedules')->insertGetId(
			    ['id'=>$newId, 'calendar_id' => $calendarId, 'json_data' => $json, 'user_id' => $userId, 'parent_id' => $parentId]
		);
		
	}

	
	/**
	 *
	 * @return date string
	 */
	public static function get($id)
	{

		return ApiSchedule::find($id);
		
	}



	
	/**
	 *
	 * @return date string
	 */
	public static function setInactive($id, $userId)
	{
		
		$S = ApiSchedule::find($id)->where('user_id', $userId)->first();

		if ( ! $S ) return false;

		$S->active = false;

		return $S->save();

	}

	
	/**
	 *
	 * @return date string
	 */
	public static function apiDelete($id, $userId)
	{

		return ApiSchedule::find($id)->where('user_id', $userId)->first()->delete();

	}
	
	/**
	 *
	 * @return date string
	 */
	public static function getRange( int $calendarId, Carbon $start, Carbon $end = null)
	{


		//$sql=" select * from api_schedules where calendar_id = ".$calendarId." and  to_date( json_data->>'scheduled_date', 'YYYY-MM-DD' ) BETWEEN '".$start->toDateString()."' AND '".$end->toDateString()."'";

		return DB::table('api_schedules')
			->whereRaw("calendar_id = {$calendarId} and  to_date( json_data->>'scheduled_date', 'YYYY-MM-DD' ) BETWEEN '{$start->toDateString()}' AND '{$end->toDateString()}'")
			->where("active", true)
			->get();

		//return \DB::select( $sql );

	}


	
	/**
	 *
	 * takes an array 
	 * @return date string
	 */
	public static function buildScheduledArray($elements, $returnJson = false)
	{

		$schs = [];
		
		foreach($elements as $el) $schs[] = $el->json_data;

		$schs = implode( $schs, ',' );

		$schs = '['.$schs.']';

		if ( $returnJson ) return $schs;

		$schs = json_decode( $schs );

		return $schs;
		
	}


	/**
	 *
	 * @return date string
	 */
	public function getDistinctScheduleIds($calendarId)
	{

		return ApiSchedule::select('id')->where('calendar_id', $calendarId)
			->where('active', true)
			->distinct()->get()->flatten();

	}

	/**
	 *
	 * @return date string
	 */
	public static function isValidScheduleJson( $jsonObject )
	{
		$sch_arr = json_decode($jsonObject);

		if ( $sch_arr === null ) return false;

		if ( ! is_int( $sch_arr->worker_id ) ) return false;

		if ( ! is_int( $sch_arr->job_length_days ) ) return false;

		return true;

	}

	
	/**
	 *
	 */
	public static function apiUpdate($calendarId, $scheduleId, $userId, $scheduleJson)
	{

		$Old = ApiSchedule::find($scheduleId);

		if ( ! $Old ||  $Old->user_id !== $userId || $Old->active !== true ) return false;

		$newId = self::add($calendarId, $userId, $scheduleJson, $Old->id);

		$Old->active = false;

		$Old->save();
		
		return $newId;
		
	}

	
	/**
	 *
	 */
	public function updateRec($target, $fields, $userId)
	{

		$schId = ( is_int($target) ) ? $target : (int)$target['schedule_id'];
		//d($schId);

		$Schedule = ApiSchedule::find( $schId);

		$data = json_decode($Schedule->json_data);

		foreach($fields as $k => $v){
			$data->$k = $v;
	  	}

		$data = json_encode($data);

		$recId = $this->apiUpdate($Schedule->calendar_id, $schId, $userId, $data);

		return ApiSchedule::find($recId);

	}
	
}
