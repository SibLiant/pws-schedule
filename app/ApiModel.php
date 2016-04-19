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


	
	/**
	 *
	 */
	public static function jsonFieldsToArray($collection, $field)
	{
		$n = [];

		foreach($collection as $c)   $n[] = $c->$field;

		$t = implode(',',$n);

		$jsonArr =  "[".$t."]";
		
		return json_decode($jsonArr);
		
	}
}

