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
	private $errors = [];


	/**
	 *
	 * @return void
	 */
	public function __construct($json)
	{

		$ob = json_decode($json);
		if($ob === null) throw new \Exception( 'invalid json format' ); 

		$this->json = $json;
	}

	public function isValid(){


		$this->checkDateRanges();
		$this->checkScheduleBlocks();

		return $this->json;

	}

	private function checkDateRanges(){ }
	private function checkScheduleBlocks(){ }

}

