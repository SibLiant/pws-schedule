<?php 
require 'vendor/autoload.php';

use GuzzleHttp\Client;
use Carbon\Carbon as Carbon;

class Scheduler {

	public $client;
	private $email = 'pws@gmail.com';
	private $pass = '1234';
	private $token;
	private $tokenDir; 
	private $tokenFilename = 'token';
	private $tokenFilePath;
	private $targetDate;

	function __construct($targetDate){

		$this->client = new Client([
			'base_uri' => 'http://localhost:8000',
			'http_errors' => false,
			'timeout'  => 2.0,
		]);
		$this->tokenDir = dirname(__FILE__);
		$this->tokenFilePath = $this->tokenDir . $this->tokenFilename;
		$this->targetDate = $targetDate;
	}


	function getJsonWebToken(){
		$post = [
			'form_params' =>[
				'email' => $this->email,
				'password' => $this->pass,
			],
			//'debug' => true
		];

		$resp = $this->client->request('POST', '/api/authenticate', $post);
		$json = json_decode($resp->getBody());
		//!Kint::dump($json); die();
		//$this->token = $json->token;
		if ( isset( $json->data->token ) ) {
			return $json->data->token;
		}
		else {
			!Kint::dump($json); die();
		}
	}

	function setToken($token) {
		$this->token = $token;
	}

	function storeToken($token){
		try{ 
			file_put_contents($this->tokenFilePath, $token);
	   	}
		catch ( \Exception $e) {
			!Kint::dump($e); die();
		}
	}

	function getTokenFromStore(){
		if ( ! file_exists( $this->tokenFilePath  )) return false;
		return file_get_contents( $this->tokenFilePath  );
	}


	function getScheduleJson($targetDate){
		return '{"auth":{"username":"parker","key":"asdfifeilsdfkjlkjsdf"},"calendarRange":{"start":"2016-03-29","end":"2016-04-28"},"scheduleRecords":{"1":{"worker_name":"YlRtlX8WuX8VloT","customer_name":"q9ve9MO3iJf1TR4","project_id":9,"customer_id":21,"worker_id":3,"schedule_id":1,"scheduled_date":"2016-04-10","job_length_days":1,"schedule_note":null,"external_link":null,"tags":[4,3,2,1]}},"settings":{"navForward":"30","navBackward":"30"},"workerRecords":[{"worker_id":1,"worker_name":"Peters"},{"worker_id":2,"worker_name":"eftC9Pban1nS6ty"},{"worker_id":3,"worker_name":"YlRtlX8WuX8VloT"},{"worker_id":4,"worker_name":"46q9wXh6d6Rypt9"},{"worker_id":5,"worker_name":"Q5qkaeLcBGgMrqj"}],"tags":{"1":{"id":1,"name":"guitar box","abbreviation":"GB","tool_tip":"testing tool tip","background_color":"blue","border_color":"grey"},"2":{"id":2,"name":"drump box","abbreviation":"PB","tool_tip":"testing tool tip","background_color":"orange","border_color":"black"},"3":{"id":3,"name":"piano box","abbreviation":"XXL","tool_tip":"testing tool tip","background_color":"silver","border_color":"yellow"},"4":{"id":4,"name":"speaker box","abbreviation":"HVHC","tool_tip":"testing tool tip","background_color":"green","border_color":"red"}}}';
	}


	function postScheduleData($json){
		$json = ($json) ? $json : $this->getScheduleJson();
		$post = [
			'form_params' =>[
				'jsonPayload' => $json,
			],
			//'debug' => true
		];

		$token =  $this->getTokenFromStore();

		if ( ! $token ) {
			$token = $this->getJsonWebToken();
			$this->storeToken($token);
		}

		$url = '/api/postJSON?token='.$token;
		$resp = $this->client->request('POST', $url, $post);
		$json = json_decode($resp->getBody());
		//!Kint::dump($json); die();
		if ( isset( $json->error ) ) echo 'Error: ' . $json->error;
	}


	function forward(){
		header("Location: http://localhost:8000/RO/postedSchedule");
		die();
	}

}


$targetDate = ( isset($_SERVER['QUERY_STRING']) ) ? $_SERVER['QUERY_STRING'] : '';

if ( $targetDate ) {
	$calStart =  Carbon::createFromFormat('Y-m-d', $targetDate)->toDateString();
	$calEnd =  Carbon::createFromFormat('Y-m-d', $targetDate)->addDays(30)->toDateString();
}
else {

	$calStart =  Carbon::now()->toDateString();
	$calEnd =  Carbon::now()->addDays(30)->toDateString();
}



$testJson =<<<json
{
	"auth": {
		"username": "parker",
		"key": "asdfifeilsdfkjlkjsdf"
	},
	"calendarRange": {
		"start": "{$calStart}",
		"end": "{$calEnd}"

	},
	"scheduleRecords": {
		"1": {
			"worker_name": "YlRtlX8WuX8VloT",
			"customer_name": "q9ve9MO3iJf1TR4",
			"project_id": 9,
			"customer_id": 21,
			"worker_id": 3,
			"schedule_id": 1,
			"scheduled_date": "2016-04-10",
			"job_length_days": 1,
			"schedule_note": null,
			"external_link": null,
			"tags": [4, 3, 2, 1]
		},
		"2": {
			"worker_name": "eftC9Pban1nS6ty",
			"customer_name": "6H9PWYEVnuCjm64",
			"project_id": 35,
			"customer_id": 23,
			"worker_id": 2,
			"schedule_id": 2,
			"scheduled_date": "2016-03-16",
			"job_length_days": 5,
			"schedule_note": null,
			"external_link": null,
			"tags": [4, 3, 2, 1]
		},
		"3": {
			"worker_name": "eftC9Pban1nS6ty",
			"customer_name": "q9ve9MO3iJf1TR4",
			"project_id": 9,
			"customer_id": 21,
			"worker_id": 2,
			"schedule_id": 3,
			"scheduled_date": "2016-03-14",
			"job_length_days": 5,
			"schedule_note": null,
			"external_link": null,
			"tags": [4, 3, 2, 1]
		},
		"4": {
			"worker_name": "eftC9Pban1nS6ty",
			"customer_name": "GIRGm4JCHDLRDAq",
			"project_id": 45,
			"customer_id": 44,
			"worker_id": 2,
			"schedule_id": 4,
			"scheduled_date": "2016-04-08",
			"job_length_days": 3,
			"schedule_note": null,
			"external_link": null,
			"tags": [4, 3, 2, 1]
		},
		"5": {
			"worker_name": "46q9wXh6d6Rypt9",
			"customer_name": "hxPS6QyqPhNbVsG",
			"project_id": 10,
			"customer_id": 16,
			"worker_id": 4,
			"schedule_id": 5,
			"scheduled_date": "2016-04-04",
			"job_length_days": 1,
			"schedule_note": null,
			"external_link": null,
			"tags": [4, 3, 2, 1]
		},
		"6": {
			"worker_name": "Peters",
			"customer_name": "hSLGkjCePZk0s9c",
			"project_id": 18,
			"customer_id": 25,
			"worker_id": 1,
			"schedule_id": 6,
			"scheduled_date": "2016-04-09",
			"job_length_days": 5,
			"schedule_note": null,
			"external_link": null,
			"tags": [4, 3, 2, 1]
		},
		"7": {
			"worker_name": "Q5qkaeLcBGgMrqj",
			"customer_name": "e3F9WmdsLj1QfqZ",
			"project_id": 5,
			"customer_id": 48,
			"worker_id": 5,
			"schedule_id": 7,
			"scheduled_date": "2016-03-23",
			"job_length_days": 4,
			"schedule_note": null,
			"external_link": null,
			"tags": [4, 3, 2, 1]
		},
		"8": {
			"worker_name": "YlRtlX8WuX8VloT",
			"customer_name": "8Ai6H6Jbz1jDLc3",
			"project_id": 41,
			"customer_id": 37,
			"worker_id": 3,
			"schedule_id": 8,
			"scheduled_date": "2016-04-12",
			"job_length_days": 1,
			"schedule_note": null,
			"external_link": null,
			"tags": [4, 3, 2, 1]
		},
		"9": {
			"worker_name": "Q5qkaeLcBGgMrqj",
			"customer_name": "LwBt2MIhWXEaY7N",
			"project_id": 21,
			"customer_id": 30,
			"worker_id": 5,
			"schedule_id": 9,
			"scheduled_date": "2016-03-24",
			"job_length_days": 5,
			"schedule_note": null,
			"external_link": null,
			"tags": [4, 3, 2, 1]
		},
		"10": {
			"worker_name": "Peters",
			"customer_name": "8Ai6H6Jbz1jDLc3",
			"project_id": 50,
			"customer_id": 37,
			"worker_id": 1,
			"schedule_id": 10,
			"scheduled_date": "2016-04-12",
			"job_length_days": 2,
			"schedule_note": null,
			"external_link": null,
			"tags": [4, 3, 2, 1]
		},
		"11": {
			"worker_name": "Peters",
			"customer_name": "8Ai6H6Jbz1jDLc3",
			"project_id": 50,
			"customer_id": 37,
			"worker_id": 1,
			"schedule_id": 11,
			"scheduled_date": "2016-03-18",
			"job_length_days": 3,
			"schedule_note": null,
			"external_link": null,
			"tags": [4, 3, 2, 1]
		},
		"12": {
			"worker_name": "Q5qkaeLcBGgMrqj",
			"customer_name": "p9gIZDpMpbxygLt",
			"project_id": 31,
			"customer_id": 1,
			"worker_id": 5,
			"schedule_id": 12,
			"scheduled_date": "2016-04-11",
			"job_length_days": 3,
			"schedule_note": null,
			"external_link": null,
			"tags": [4, 3, 2, 1]
		},
		"13": {
			"worker_name": "Q5qkaeLcBGgMrqj",
			"customer_name": "IjMZ5uyn2pHRdMD",
			"project_id": 22,
			"customer_id": 8,
			"worker_id": 5,
			"schedule_id": 13,
			"scheduled_date": "2016-04-01",
			"job_length_days": 3,
			"schedule_note": null,
			"external_link": null,
			"tags": [4, 3, 2, 1]
		},
		"14": {
			"worker_name": "46q9wXh6d6Rypt9",
			"customer_name": "QzMbi4cY9iT31DO",
			"project_id": 37,
			"customer_id": 43,
			"worker_id": 4,
			"schedule_id": 14,
			"scheduled_date": "2016-03-27",
			"job_length_days": 5,
			"schedule_note": null,
			"external_link": null,
			"tags": [4, 3, 2, 1]
		},
		"201": {
			"worker_name": "Peters",
			"customer_name": "Mr. Parker Bradtmiller",
			"project_id": 201,
			"customer_id": 51,
			"worker_id": 1,
			"schedule_id": 201,
			"scheduled_date": "2016-03-14",
			"job_length_days": 3,
			"schedule_note": null,
			"external_link": null
		}
	},
	"settings": {
		"navForward": "30",
		"navBackward": "30",
		"navRootUrl": "http://localhost:8001"
	},
	"workerRecords": [{
		"worker_id": 1,
		"worker_name": "Peters"
	}, {
		"worker_id": 2,
		"worker_name": "eftC9Pban1nS6ty"
	}, {
		"worker_id": 3,
		"worker_name": "YlRtlX8WuX8VloT"
	}, {
		"worker_id": 4,
		"worker_name": "46q9wXh6d6Rypt9"
	}, {
		"worker_id": 5,
		"worker_name": "Q5qkaeLcBGgMrqj"
	}],
	"tags": {
		"1": {
			"id": 1,
			"name": "guitar box",
			"abbreviation": "GB",
			"tool_tip": "testing tool tip",
			"background_color": "blue",
			"border_color": "grey"
		},
		"2": {
			"id": 2,
			"name": "drump box",
			"abbreviation": "PB",
			"tool_tip": "testing tool tip",
			"background_color": "orange",
			"border_color": "black"
		},
		"3": {
			"id": 3,
			"name": "piano box",
			"abbreviation": "XXL",
			"tool_tip": "testing tool tip",
			"background_color": "silver",
			"border_color": "yellow"
		},
		"4": {
			"id": 4,
			"name": "speaker box",
			"abbreviation": "HVHC",
			"tool_tip": "testing tool tip",
			"background_color": "green",
			"border_color": "red"
		}
	}
}
json;

$sch = new Scheduler($targetDate);
$sch->postScheduleData($testJson);
$sch->forward();

?>
