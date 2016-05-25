<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kint;
use App\ApiCalendar;
use App\ApiCalendarWorkerJoin;

class ApiTag extends ApiModel
{
	use SoftDeletes;

	public $timestamps = true;

	protected $guarded = ['id'];
	
	protected $casts = [ 'tag_json' => 'array' ];

	public $jsonDefaults = [
		'name' => 'Tag',
		'background_color' => 'green',
		'abbreviation' => 'ZZZZ',
		'tool_tip' => 'ZZZZ'
	];

	public function user()
	{
		
		return $this->belongsTo('App\User');
					
	}

	/**
	 *
	 * @return date string
	 */
	public static function add($userId, $json)
	{

		$tagArr = json_decode($json);

		if ( ! $tagArr ) return false;

		$newId = self::nextVal('api_tags_id_seq');

		$tagArr->tag_id = $newId;

		$json = json_encode($tagArr);

		return \DB::table('api_tags')->insertGetId(
			    ['id'=>$newId, 'tag_json' => $json, 'user_id' => $userId]
		);
		
	}

	/**
	 *
	 * @return date string
	 */
	public static function remove($userId, $tagId)
	{

		$Tag = ApiTag::find($tagId);

		if ( ! $Tag ) return false;

		if ( $Tag->user_id == $userId ) {

			$Tag->delete();

			return true;
		}

		return false;
	}

	/**
	 *
	 * @return date string
	 */
	public static function get($tagId, $userId)
	{
		 $Tag = ApiTag::find($tagId);

		 if ( ! $Tag ) return false;

		 if ( $Tag->user_id !== $userId ) return false;

		 return $Tag;
		
	}	

	/**
	 *
	 * @return date string
	 */
	public static function isValidJson($json)
	{
		$j = json_decode( $json );

		if ( ! $j ) return false;

		if ( ! isset($j->name) ) return false;

		if ( ! isset($j->abbreviation) ) return false;

		if ( ! isset($j->background_color) ) return false;

		return true;
	}

	
	/**
	 *
	 */
	public function formatForSchedule($collection)
	{

		$new = [];

		foreach($collection as $k => $v){

			$new[$k] = $v;

	  	}

		return $new;
		
	}

	
	/**
	 *
	 */
	public static function tList($userId)
	{

		$tags = ApiTag::where('user_id', $userId)->get();


		if ( ! $tags ) return [];

		$list = [];


		foreach($tags as $k => $v ) {

			$list[$v['id']] = $v['tag_json']['name'];
			
		}

		return $list;
		
	}

}
