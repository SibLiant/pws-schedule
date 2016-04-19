<?php 

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\ApiSchedule;

class PWS_JSON_API_ControllerTest extends TestCase
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
	public function it_creates_a_new_calendar()
	{

		$d = [ 'calendarJson' => '{"name":"parkers cool cal","data_element":1}' ];

		$resp = $this->clientJson( 'POST', '/api/calendar/add', $d, []);

		$resp->seeJsonStructure(['data' => [ 'type', 'id' ]]);
		
	}

	/** * @test **/
	public function it_removes_an_existing_calendar()
	{
		
		$tmp = factory( App\ApiCalendar::class, 1 )->create(['calendar_json' => '{"name":"whateer"}', 'id'=>300, 'user_id' => $this->client->id]);

		$resp = $this->clientGET('/api/calendar/remove/300', []);

		$resp->seeJsonStructure(['data' => ['type','remove']]);
		
	}

	/** * @test **/
	public function it_gets_a_calendar()
	{

		$tmp = factory( App\ApiCalendar::class, 1 )->create(['calendar_json' => '{"name":"whateer"}', 'id'=>300, 'user_id' => $this->client->id]);

		$resp = $this->clientGET('/api/calendar/get/300', []);
		
		$resp->seeJsonStructure(['data' => ['name']]);

	}

	/** * @test **/
	public function it_adds_a_worker()
	{

		$d = [ 'workerJson' => '{"worker_name":"parkers cool worker","data_element":1}' ];

		$resp = $this->clientJson( 'POST', '/api/worker/add', $d, []);

		$resp->seeJsonStructure(['data' => [ 'type', 'id' ]]);

	}

	/** * @test **/
	public function it_removes_a_worker()
	{

		$tmp = factory( App\ApiWorker::class, 1 )->create(['worker_json' => '{"name":"worker name"}', 'id'=>500, 'user_id' => $this->client->id]);

		$resp = $this->clientGET('/api/worker/remove/500', []);

		$resp->seeJsonStructure(['data' => ['type','remove']]);

	}

	/** * @test **/
	public function it_gets_a_worker()
	{

		$tmp = factory( App\ApiWorker::class, 1 )->create(['worker_json' => '{"name":"whateer"}', 'id'=>500, 'user_id' => $this->client->id]);

		$resp = $this->clientGET('/api/worker/get/500', []);
		
		$resp->seeJsonStructure(['data' => ['name']]);
		
	}

	/** * @test **/
	public function it_adds_an_existing_worker_to_an_existing_calendar()
	{
		
		$tmp = factory( App\ApiWorker::class, 1 )->create(['worker_json' => '{"name":"whateer"}', 'id'=>500, 'user_id' => $this->client->id]);

		$tmp = factory( App\ApiCalendar::class, 1 )->create(['calendar_json' => '{"name":"my calendar"}', 'id'=>66, 'user_id' => $this->client->id]);

		$resp = $this->clientGET('/api/worker/500/add-to-cal/66', []);

		$resp->seeJsonStructure(['data' => ['type', 'id']]);
		
	}

	/** * @test **/
	public function it_removes_a_worker_from_a_calendar()
	{

		$tmp = factory( App\ApiWorker::class, 1 )->create(['id' => 345,'user_id' => $this->client->id]);

		$tmp = factory( App\ApiCalendar::class, 1 )->create(['id' => 25,'user_id' => $this->client->id]);

		$tmp = factory( App\ApiCalendarWorkerJoin::class, 1 )->create(['worker_id' => 20, 'calendar_id' => 25]);

		$resp = $this->clientGET('/api/worker/345/remove-from-cal/25', []);

		$resp->seeJsonStructure(['data' => ['type','status']]);

	}

	/** * @test **/
	public function it_adds_a_schedule()
	{

		$d = [ 
			'scheduleJson' => '{"scheduled_date":"2016-04-01","job_length_days":3, "worker_id":4}',
			'calendarId' =>12
		];

		$tmp = factory( App\ApiCalendar::class, 1 )->create(['id' => 12, 'user_id' => $this->client->id]);

		$resp = $this->clientJson( 'POST', '/api/schedule/add', $d, []);

		$resp->seeJsonStructure(['data' => [ 'type', 'id' ]]);
		
	}

	/** * @test **/
	public function it_delets_a_schedule_element()
	{

		$tmp = factory( App\ApiSchedule::class, 1 )->create(['id' => 433, 'user_id' => $this->client->id]);

		$resp = $this->clientGet( '/api/schedule/remove/433', []);
		//$resp->dump();

		$resp->seeJsonStructure(['data' => [ 'type', 'status' ]]);

		
	}


	/** * @test **/
	public function it_gets_a_schedule_element()
	{
		$tmp = factory( App\ApiSchedule::class, 1 )->create(['id' => 222, 'user_id' => $this->client->id]);

		$resp = $this->clientGet( '/api/schedule/get/222', []);

		$resp->seeJsonStructure(['data' => [ 'job_length_days', 'worker_id' ]]);
		
	}

	/** * @test **/
	public function it_sets_a_schedule_element_inactive()
	{

		$tmp = factory( App\ApiSchedule::class, 1 )->create(['id' => 444, 'user_id' => $this->client->id]);

		$resp = $this->clientGet( '/api/schedule/setInactive/444', []);

		$resp->seeJsonStructure(['data' => [ 'type', 'status' ]]);
		
	}

	
	/** @test * */
	public function it_updates_a_schedule_element()
	{

		$tmp = factory( App\ApiCalendar::class, 1 )->create(['id' => 18, 'user_id' => $this->client->id ]);

		$tmp = factory( App\ApiSchedule::class, 1 )->create(['id' => 444, 'user_id' => $this->client->id ]);

		$j ='{"testing":"testing","scheduled_date":"2016-04-01","job_length_days":3,"worker_id":3}' ;

		$d = ['scheduleId' => 444, 'scheduleJson' => $j, 'calendarId' => 18 ];

		$resp = $this->clientJson( 'POST', '/api/schedule/update', $d, []);

		$resp->seeJsonStructure(['data' => [ 'type', 'id' ]]);
		
	}

}

?>
