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


		if ( $id = self::getJoinId($calendarId, $workerId) ) {

			return  ApiCalendarWorkerJoin::findOrFail($id)->delete();

		}

		return false;
		
	}

	
	/**
	 *
	 * @return date string
	 */
	public static function getJoinId($calendarId, $workerId)
	{
		$j = ApiCalendarWorkerJoin::where('calendar_id', $calendarId)
			->where('worker_id', $workerId)
			->value('id');

		if ( $j ) return $j;

		return false;

	}


}

