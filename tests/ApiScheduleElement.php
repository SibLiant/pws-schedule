<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\ApiSchedule;

class ApiScheduleElementTest extends TestCase
{


	use DatabaseTransactions;

	
	/**
	 *
	 * @test 
	 */
	public function testFindByScheduleId()
	{

		$E = new ApiSchedule();

		factory( App\ApiSchedule::class, 4 )->create([]);

		$recs = $E->findByScheduleId(1, 1234 );

		$this->assertEquals( count( $recs ), 4 );

	}


	
	/**
	 *
	 * @test
	 */
	public function testFindByCalendarId()
	{
		

		DB::table('api_schedules')->truncate();

		factory( App\ApiSchedule::class, 4 )->create(['calendar_id' => 2]);

		$E = new ApiSchedule();

		$recs = $E->findAllByCalendarId(2);

		$this->assertEquals( count( $recs ), 4 );

	}

	
	/**
	 *
	 * @return date string
	 */
	public function testFindByWorkerId()
	{

		DB::table('api_schedules')->truncate();

		factory( App\ApiSchedule::class, 1 )->create(['json_data' => '{"worker_id":"99"}']);
		factory( App\ApiSchedule::class, 1 )->create(['json_data' => '{"worker_id":"99"}']);
		
		//$E = new ApiSchedule();
		//$recs = $E->findByWorkerId(1, 99);


	}


	/**
	 *
	 * @return date string
	 */
	public function testGetDistinctScheduleIds()
	{
		$E = new ApiSchedule();

		DB::table('api_schedules')->truncate();

		factory( App\ApiSchedule::class, 1 )->create([ 'json_data' => '{"schedule_id":"1"}' ]);
		factory( App\ApiSchedule::class, 1 )->create([ 'json_data' => '{"schedule_id":"2"}' ]);
		factory( App\ApiSchedule::class, 1 )->create([ 'json_data' => '{"schedule_id":"2"}' ]);
		factory( App\ApiSchedule::class, 1 )->create([ 'json_data' => '{"schedule_id":"4"}' ]);

		$recs = $E->getDistinctScheduleIds(1);

		$this->assertEquals( count($recs), 3 );
		
	}


	
	/**
	 *
	 * @return date string
	 */
	public function testIsValidScheduleJson()
	{

		$E = new ApiSchedule();

		//valid
		$json = '{ "worker_name": "mark", "customer_name": "john", "project_id": 21, "customer_id": 30, "worker_id": 5, "schedule_id": 9, "scheduled_date": "2016-03-24", "job_length_days": 5, "schedule_note": null, "external_link": null, "tags": [4, 3, 2, 1] }'; 
		$this->assertTrue( $E->isValidScheduleJson( $json ) );

		//invalid  worker id
		$json = '{ "worker_name": "mark", "customer_name": "john", "project_id": 21, "customer_id": 30, "worker_id": 5.5, "schedule_id": 9, "scheduled_date": "2016-03-24", "job_length_days": 5, "schedule_note": null, "external_link": null, "tags": [4, 3, 2, 1] }'; 
		$this->assertFalse( $E->isValidScheduleJson( $json ) );


		//invalid  job length
		$json = '{ "worker_name": "mark", "customer_name": "john", "project_id": 21, "customer_id": 30, "worker_id": 5, "schedule_id": 9, "scheduled_date": "2016-03-24", "job_length_days": "a", "schedule_note": null, "external_link": null, "tags": [4, 3, 2, 1] }'; 
		$this->assertFalse( $E->isValidScheduleJson( $json ) );
	}

	
	/**
	 *
	 * @return date string
	 */
	//public function testApiAdd()
	//{

		//$client = factory(App\Calendar::class)->create(['name' => 'api testing', 'id' => 3, 'user_id' => $this->client->id]);

		//$postData = [
			//'scheduleJson'=>'{ "worker_name": "mark", "customer_name": "john", "project_id": 21, "customer_id": 30, "worker_id": 5, "schedule_id": 9, "scheduled_date": "2016-03-24", "job_length_days": 5, "schedule_note": null, "external_link": null, "tags": [4, 3, 2, 1]  }',
			//'calendarId'=>3
		//];
		//$resp = $this->clientJson( 'POST', '/api/schedule/add', $postData, []);


		//$resp->seeJsonStructure(["data"=>["type", "id"]]);

	//}

	
	/**
	 *
	 * @return date string
	 */
	//public function testApiAddError()
	//{

		//$client = factory(App\Calendar::class)->create(['name' => 'api testing', 'id' => 3, 'user_id' => $this->client->id]);

		////invalid worker id post
		//$postData = [
			//'scheduleJson'=>'{ "worker_name": "mark", "customer_name": "john", "project_id": 21, "customer_id": 30, "worker_id": "a", "schedule_id": 9, "scheduled_date": "2016-03-24", "job_length_days": 5, "schedule_note": null, "external_link": null, "tags": [4, 3, 2, 1]  }',
			//'calendarId'=>3
		//];
		//$resp = $this->clientJson( 'POST', '/api/schedule/add', $postData, []);

		//$resp->seeJsonStructure(["errors"]);
		
	//}

	
	/**
	 *
	 * @return date string
	 */
	public function testApiGet()
	{
		$E = new ApiSchedule();

		$resp = $E->get(1,1)->toArray();

		$this->assertEquals(1, $resp[0]['id']);
	}

}
