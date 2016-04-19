<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Kint;

class ApiCalendarWorkerJoin extends ApiModel
{


	public $timestamps = false;
	
	/**
	 *
	 * @return date string
	 */
	public static function join($calendarId, $workerId)
	{

		if ( $id = self::getJoinId($calendarId, $workerId)  ) {

			return $id;

		} else {
			
			$J = new ApiCalendarWorkerJoin;

			$J->calendar_id = $calendarId;

			$J->worker_id = $workerId;

			$J->save();

			return $J->id;
		}
		
	}

	
	/**
	 *
	 */
	public static function unJoin($calendarId, $workerId)
	{
		$id = self::getJoinId($calendarId, $workerId);

		ApiCalendarWorkerJoin::where('calendar_id', $calendarId)->where('worker_id', $workerId)->delete();

		return true;
		
	}

	
	/**
	 *
	 * @return date string
	 */
	public static function getJoinId($calendarId, $workerId)
	{
		$J = ApiCalendarWorkerJoin::select('*')->where('calendar_id', $calendarId)->where("worker_id", $workerId)->get();

		if ( $J->isEmpty() ) return false;

		return $J->id;;

	}


}

