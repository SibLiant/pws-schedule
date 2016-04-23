<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Kint;

class ApiModel extends Model
{
    

	/**
	 *
	 * @return int
	 */
	public static function nextVal($sequenceName)
	{

		$sql = "select nextval('{$sequenceName}')";

		$res = \DB::select($sql);

		return $res[0]->nextval;
		
	}


}

