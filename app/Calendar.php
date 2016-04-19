<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \Kint;

class Calendar extends Model
{


	/**
	 *
	 * @return boolearn
	 */
	public static function userOwnsCalendar($calendarId, $userId)
	{
		return Calendar::where('user_id', $userId)->get()->contains('id', $calendarId);
		
	}

	
	/**
	 *
	 * @return date string
	 */
	public static function isValidJson()
	{
		
		return true;

	}
	

}
