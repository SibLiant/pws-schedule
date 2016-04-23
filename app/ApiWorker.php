<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kint;
use App\ApiCalendar;
use App\ApiCalendarWorkerJoin;

class ApiWorker extends ApiModel
{

	use SoftDeletes;

	public $timestamps = true;

	protected $guarded = ['id', 'created_at', 'updated_at'];
	
	protected $casts = [ 'worker_json' => 'array' ];

	public $jsonDefaults = [
		'worker_name' => '',
		'external_worker_id' => ''
	];

	public function calendars()
	{
		
		return $this->belongsToMany('App\ApiCalendar', 'api_calendar_worker_joins', 'calendar_id', 'worker_id');
					
	}
	
	/**
	 *
	 * @return date string
	 */
	public static function add($userId, $json)
	{

		$workerArr = json_decode($json);

		if ( ! $workerArr ) return false;

		$newId = self::nextVal('api_workers_id_seq');

		$workerArr->worker_id = $newId;

		$json = json_encode($workerArr);

		return \DB::table('api_workers')->insertGetId(
			    ['id'=>$newId, 'worker_json' => $json, 'user_id' => $userId]
		);
		
	}

	
	/**
	 *
	 * @return date string
	 */
	public static function remove($userId, $workerId)
	{

		$Worker = ApiWorker::find($workerId);

		if ( ! $Worker ) return false;

		if ( $Worker->user_id == $userId ) {
			$Worker->delete();
			return true;
		}

		return false;
	}


	
	/**
	 *
	 * @return date string
	 */
	public static function get($workerId, $userId)
	{
		 $Worker = ApiWorker::find($workerId);

		 if ( ! $Worker ) return false;

		 if ( $Worker->user_id !== $userId ) return false;

		 return $Worker;
		
	}	

	
	/**
	 *
	 * @return date string
	 */
	public static function isValidJson($json)
	{
		$j = json_decode( $json );

		if ( ! $j ) return false;

		if ( ! isset($j->worker_name) ) return false;

		return true;
	}

	
	/**
	 *
	 * @return date string
	 */
	public static function addWorkerToCalendar($workerId, $calendarId)
	{
		if ( $joinId = ApiCalendarWorkerJoin::join($calendarId, $workerId) ) {

			return $joinId;

		}
		
		return false;
	}

	
	/**
	 *
	 */
	public static function removeWorkerFromCalendar($calendarId, $workerId)
	{

		return ApiCalendarWorkerJoin::unJoin($calendarId, $workerId);

	}

	
	/**
	 *
	 * @return date string
	 */
	public function getWorkersByCalendarId($calendarId, $userId)
	{

		$res = ApiCalendar::find($calendarId);

		$res = ApiCalendarWorkerJoin::where('calendar_id', $calendarId)->get()->toArray();

		if ( empty( $res ) )  return false;

		$workerArr = [];

		foreach( $res as $w ){ $workerArr[] = $w[ 'worker_id' ]; }

		return ApiWorker::whereIn('id', $workerArr)->where('user_id', $userId)->get()->toArray();

		
	}

}
