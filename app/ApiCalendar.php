<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Kint;

class ApiCalendar extends ApiModel
{
	public $timestamps = false;


	public function workers()
	{
		
		return $this->belongsToMany('App\ApiWorker', 'api_calendar_worker_joins', 'calendar_id', 'worker_id');
					
	}
	
	/**
	 *
	 * @return date string
	 */
	public static function add($userId, $calendarJson)
	{

		$calArr = json_decode($calendarJson);

		if ( ! $calArr ) return false;


		$newId = self::nextVal('api_calendars_id_seq');

		$calArr->calendar_id = $newId;

		$json = json_encode($calArr);

		return \DB::table('api_calendars')->insertGetId(
			    ['id'=>$newId, 'calendar_json' => $json, 'user_id' => $userId]
		);
		
	}

	
	/**
	 *
	 * @return date string
	 */
	public static function remove($id, $userId)
	{
		if ( self::userOwnsCalendar($id, $userId) ) {

			$Cal = ApiCalendar::find($id);

			$Cal->delete();

			return true;

		}

		return false;


	}

	
	/**
	 *
	 * @return date string
	 */
	public static function get($id, $userId)
	{

		$Cal = ApiCalendar::find($id); 

		if ( $Cal && $Cal->user_id == $userId ) return $Cal;

		return false;
		
	}


	/**
	 *
	 * @return boolearn
	 */
	public static function userOwnsCalendar($calendarId, $userId)
	{

		return ApiCalendar::where('user_id', $userId)->get()->contains('id', $calendarId);
		
	}

	
	/**
	 *
	 * @return date string
	 */
	public static function isValidJson($json)
	{
		
		$j = json_decode($json);

		if ( ! $j ) return false;

		return true;

	}
}
