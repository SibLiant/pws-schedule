<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CalendarInvitation extends Model
{
    //
	use SoftDeletes;

	protected $guarded = ['id'];



	
	/**
	 *
	 */
	public function calendar()
	{
		
		return belongsTo('App\ApiCalendar');

	}

	

}
