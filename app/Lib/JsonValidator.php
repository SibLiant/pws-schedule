<?php namespace App\Lib;

use Carbon\Carbon as Carbon;
use \Kint as Kint;

//use Illuminate\Database\Eloquent\Model;

/**
 * undocumented class
 *
 * @package default
 * @subpackage default
 * @author Parker Bradtmiller
 */
class JsonValidator
{

	private $json;
	private $isValid = false;


	/**
	 *
	 * @return void
	 */
	public function __construct($json)
	{

		$this->json = $json;

		$this->isValid = $this->isValid($this->json);

	}

	static function isValid($json){

		$ob = json_decode($json);

		if($ob === null) return false;

		return true;
	}





}

