<?php 

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\ApiWorker;
use App\ApiCalendarWorkerJoin;

class ApiWorkerTest extends TestCase
{

	use DatabaseTransactions;


	private $_object;
	private $_data;
	
	protected function setUp()
	{
			parent::setUp();

			$this->_object = new ApiWorker;
	
			$this->_data = '{"worker_name":"worker worker","some_data":"parker"}';
	}


	/** * @test **/
	public function it_adds_a_worker()
	{
	
		$id = $this->_object->add($this->client->id, $this->_data, 8);

		$this->assertInternalType("int", $id);

		$W = $this->_object->find($id);

		$this->assertEquals($W->id, $id);
		
	}

	/** * @test **/
	public function it_removes_a_worker()
	{

		$tmp = factory( App\ApiWorker::class, 1 )->create(['worker_json' => '{"worker_id":"99"}', 'id'=>300, 'user_id' => $this->client->id]);

		$res = $this->_object->remove($this->client->id, 300);

		$this->assertTrue($res);
		
	}


	/** * @test **/
	public function it_gets_a_worker()
	{
		
		$tmp = factory( App\ApiWorker::class, 1 )->create(['worker_json' => '{"worker_id":"99"}', 'id'=>300, 'user_id' => $this->client->id]);

		$W = $this->_object->get(300, $this->client->id);

		$this->assertEquals( $W->id, 300 );

	}

	/** * @test **/
	public function it_validates_json()
	{

		$this->assertTrue( $this->_object->isValidJson($this->_data)  );

		$this->assertFalse( $this->_object->isValidJson('{"name":"test name"}')  );
		

	}

	/** * @test **/
	public function it_joins_a_worker_to_an_existing_calendar()
	{
		
		//add a cal and worker and join them
		$tmp = factory( App\ApiCalendar::class, 1 )->create(['calendar_json' => '{"name":"pizark"}', 'id'=>300, 'user_id' => $this->client->id]);

		$tmp = factory( App\ApiWorker::class, 1 )->create(['worker_json' => '{"name":"my worker"}', 'id'=>20, 'user_id' => $this->client->id]);

		$id = $this->_object->addWorkerToCalendar(20, 300);
		
		$this->assertInternalType("int", $id);
		
	}

	/** * @test **/
	public function it_gets_all_workers_by_a_calendar_id()
	{


		$tmp = factory( App\ApiCalendar::class, 1 )->create(['calendar_json' => '{"name":"pizark"}', 'id'=>300, 'user_id' => $this->client->id]);

		$tmp = factory( App\ApiWorker::class, 1 )->create(['worker_json' => '{"name":"my worker40"}', 'id'=>40, 'user_id' => $this->client->id]);
		$tmp = factory( App\ApiWorker::class, 1 )->create(['worker_json' => '{"name":"my worker41"}', 'id'=>41, 'user_id' => $this->client->id]);
		$tmp = factory( App\ApiWorker::class, 1 )->create(['worker_json' => '{"name":"my worker42"}', 'id'=>42, 'user_id' => $this->client->id]);

		//join these workes to the cals
		$this->_object->addWorkerToCalendar( 40, 300 );
		$this->_object->addWorkerToCalendar( 41, 300 );
		$this->_object->addWorkerToCalendar( 42, 300 );

		$recs = $this->_object->getWorkersByCalendarId( 300, $this->client->id );

		$this->assertCount(3, $recs);
		
	}



}

?>
