<?php 

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\ApiSchedule;
use Carbon\Carbon;

class ApiScheduleTest extends TestCase
{

	use DatabaseTransactions;


	private $_object;
	private $_data;
	
	protected function setUp()
	{
			parent::setUp();

			$this->_object = new ApiSchedule;
	
			$this->_data = '';
	}

	/** * @test **/
	public function it_adds_an_element()
	{

		$postData = [
			'scheduleJson'=>'{ "worker_name": "mark", "customer_name": "john", "project_id": 21, "customer_id": 30, "worker_id": 5, "schedule_id": 9, "scheduled_date": "2016-03-24", "job_length_days": 5, "schedule_note": null, "external_link": null, "tags": [4, 3, 2, 1]  }',
			'calendarId'=>300
		];

		$res = $this->_object->add(1, 1, $postData['scheduleJson'] );

		$this->assertInternalType("int", $res);

	}


	/** * @test **/
	public function it_deletes_an_element_from_the_database_completely()
	{

		//add a record so we can delete it
		$tmp = factory( App\ApiSchedule::class, 1 )->create(['json_data' => '{"worker_id":"99"}', 'id'=>300, 'user_id' => $this->client->id]);

		$res = $this->_object->apiDelete(300, $this->client->id);

		$this->assertTrue( $res );
		
	}

	/** * @test **/
	public function it_sets_an_element_as_inactive()
	{
		
		//add a record so we can deactivate it
		$tmp = factory( App\ApiSchedule::class, 1 )->create(['json_data' => '{"worker_id":"99"}', 'id'=>300, 'user_id' => $this->client->id]);
		
		
		$res = $this->_object->setInactive(300, $this->client->id);

		$this->assertTrue( $res );
		
	}


	/** * @test **/
	public function it_finds_records_by_calendar_id()
	{

		$tmp = factory( App\ApiSchedule::class, 5 )->create(['calendar_id'=>200, 'json_data' => '{"worker_id":"99"}', 'user_id' => $this->client->id]);
		
		$recs = $this->_object->findByCalendarId(200);

		$this->assertCount(  5, $recs  );

		//add one more inactive and get them all 
		$tmp = factory( App\ApiSchedule::class, 1 )->create(['calendar_id'=>200, 'json_data' => '{"worker_id":"99"}', 'user_id' => $this->client->id, 'active' => false]);

		$recs = $this->_object->findByCalendarId(200, false);

		$this->assertCount(  6, $recs  );
	}

	/** * @test **/
	public function it_validates_json_for_api_schedule_table()
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

	

	/** * @test **/
	public function it_finds_records_on_a_calendar_by_worker_id()
	{

		$tmp = factory( App\ApiSchedule::class, 5 )->create(['calendar_id'=>200, 'json_data' => '{"worker_id":123}', 'user_id' => $this->client->id]);

		$recs = $this->_object->findByWorkerId(200, 123);

		$this->assertCount( 5, $recs );
		
	}


	/** * @test **/
	public function it_gets_an_element_by_id()
	{
		
		$tmp = factory( App\ApiSchedule::class, 1 )->create(['id' => 1000,  'calendar_id'=>1, 'json_data' => '{"worker_id":999}', 'user_id' => $this->client->id]);

		$S = $this->_object->get(1000);

		$this->assertEquals($S->id, 1000 );
		
	}

	

	/** * @test **/
	public function it_gets_all_records_of_a_calendar_by_a_date_range()
	{

		$tmp = factory( App\ApiSchedule::class, 1 )->create(['calendar_id'=>1, 'json_data' => '{"worker_id":999, "scheduled_date":"2016-01-01", "job_length_days":1}', 'user_id' => $this->client->id]);
		$tmp = factory( App\ApiSchedule::class, 1 )->create(['calendar_id'=>1, 'json_data' => '{"worker_id":999, "scheduled_date":"2016-01-30", "job_length_days":1}', 'user_id' => $this->client->id]);
		$tmp = factory( App\ApiSchedule::class, 1 )->create(['calendar_id'=>1, 'json_data' => '{"worker_id":999, "scheduled_date":"2016-01-24", "job_length_days":1}', 'user_id' => $this->client->id]);

		$recs = $this->_object->getRange(
			'1', 
			new Carbon( '2016-01-01' ), 
			new Carbon('2016-01-30')  
		);

		$this->assertCount( 3, $recs  );
		
	}

	/** * @test **/
	public function it_takes_a_calendar_id_and_returns_all_distinct_schedule_ids()
	{
		
		//create 2 schedule elements on a unique calendar id
		$tmp = factory( App\ApiSchedule::class, 1 )->create([ 'id' => '5999', 'calendar_id'=>5, 'json_data' => '{"worker_id":999, "scheduled_date":"2016-01-01", "job_length_days":1}', 'user_id' => $this->client->id]);
		$tmp = factory( App\ApiSchedule::class, 1 )->create([ 'id' => '6000', 'calendar_id'=>5, 'json_data' => '{"worker_id":999, "scheduled_date":"2016-01-01", "job_length_days":1}', 'user_id' => $this->client->id]);

		$recs = $this->_object->getDistinctScheduleIds( 5 );

		//assert that we have the exact id's and only them
		$this->assertCount(2, $recs);

		$this->assertEquals(5999, $recs[0]->id);

		$this->assertEquals(6000, $recs[1]->id);
	}

}

?>
