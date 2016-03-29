<?php namespace App\Lib;

use Carbon\Carbon as Carbon;
use App\Lib\JsonValidator;

//use Illuminate\Database\Eloquent\Model;

/**
 * undocumented class
 *
 * @package default
 * @subpackage default
 * @author Parker Bradtmiller
 */
class PWS_JSON_Generator
{

	public $randomizeTestData = false;
	public $randomGenJsonFile;


	/**
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->sDate = new Carbon();	
		$this->sDateRange = 30;	
		$this->randomGenJsonFile =  public_path() .DIRECTORY_SEPARATOR. 'random_generated_json.json';
		if ( ! file_exists( $this->randomGenJsonFile ) ) touch( $this->randomGenJsonFile );

	}

	/**
	 *
	 * @return json
	 */
	public function getJsonScheduleData()
	{
		return json_encode( ['customer_name' => 'Parker Bradtmiller'] );
	}

	/**
	 *
	 * @return json		
	 */
	public function getLocalTestJson()
	{
		return file_get_contents( public_path() .DIRECTORY_SEPARATOR. 'test_json.txt' );

	}


	/**
	 * generate a random array for testing purposes
	 * @return json
	 */
	public function getJSON()
	{
		$data = [
			"auth" => ["username" => "parker", "key" => "asdfifeilsdfkjlkjsdf"],
			"calendarRange" => [
				"start" => $this->sDate->toDateString(),
				"end" => $this->sDate->addDays($this->sDateRange)->toDateString()
			]
		];
		$data['scheduleRecords'] = $this->generateRandomScheduleRecords();
		$data['workerRecords'] = $this->parseWorkerData($data['scheduleRecords']);
		$data['settings'] = [
			'navForward' => '30', 
			'navBackward' => '30'
		];
		$data['tags'] = [];

		return json_encode( $data );
	}

	/**
	 *
	 * @return array
	 */
	public function generateRandomScheduleRecords()
	{
		
		$record_count = 160;
		$names = ['shane', 'roberts', 'jacobs', 'howser', 'jones', 'orwell', 'joes awesome team', 'another brick in the wall', 'jefferson', 'howards', 'seal team 6', 'neitchie', 'jettson', 'we are the millter', 'bird'];

		$recs = [];
		for( $i=0; $i < $record_count; $i++ ){
			$cust_proj_id = rand(1, 60);
			$cust_name = $this->generateRandomName();
			$worker_id = rand(0, 14);
			$schedule_id = $i;
			$r = [
				"worker_id" => $worker_id,
				"project_id" => $cust_proj_id,
				"customer_id" => $cust_proj_id,
				"schedule_id" => $schedule_id,
				"worker_name" =>  $names[$worker_id],
				"customer_name" => $cust_name, 
				"scheduled_date" => $this->generateDate(),
				"external_link" =>"http://google.com",
				"job_length_days" => rand(1, 5),
				"schedule_note" => "some random schedule note"
			];
			array_push( $recs, $r );
		}
		return $recs;
	}

	/**
	 *
	 * @return array
	 */
	public function parseWorkerData($records)
	{
		$workers = [];
		foreach ($records as $r) {
			if ( ! array_key_exists( $r['worker_id'], $workers ) ) $workers[$r['worker_id']] = $r['worker_name'];
		}
		return $workers;
	}

	/**
	 *
	 * @return date string
	 */
	public function generateDate()
	{
		$dateSeed = rand(0, 34) -4;

		$dt = new Carbon();
		if ( $dateSeed > 0 ) $dt->addDays( $dateSeed );
		if ( $dateSeed < 0 ) $dt->subDays( $dateSeed );
		return $dt->toDateString();
	}

	function generateRandomName() {
		$length = rand(4, 20);
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_ ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

	/**
	 *
	 * @return bool
	 */
	public function isValidJson($json)
	{
		$val = new JsonValidator($json);
		return true;
	}

	
	/**
	 *
	 * @return date string
	 */
	public function writeRandomGeneratedJsonFile($json)
	{
		$fp = fopen('random_generated_json.json', 'w');
		fwrite($fp, $json);
		fclose($fp);	
	}
	
	/**
	 *
	 * @return date string
	 */
	public function readRandomGeneratedJsonFile()
	{
		return file_get_contents( public_path() .DIRECTORY_SEPARATOR. 'random_generated_json.json' );
	}

	
	/**
	 *
	 * @return date string
	 */
	public function getTestData()
	{
		if ( $this->randomizeTestData ) {
			$json = $this->generateRandomJson();
			$this->writeRandomGeneratedJsonFile($json);
			return $json;
		}
		else{
			return $this->readRandomGeneratedJsonFile();

		}
	}

}

