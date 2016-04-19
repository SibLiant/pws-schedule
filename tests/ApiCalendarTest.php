<?php 

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\ApiCalendar;

class ApiCalendarTest extends TestCase
{

	use DatabaseTransactions;


	private $_object;
	private $_data;
	
	protected function setUp()
	{
			parent::setUp();

			$this->_object = new ApiCalendar;
	
			$this->_data = '{"name":"testing","some_data":"parker"}';
	}

	/** * @test **/
	public function it_adds_a_new_calendar()
	{

		$id = $this->_object->add($this->client->id, $this->_data);

		$C = $this->_object->find($id);

		$this->assertEquals($C->id, $id);
		
	}

	/** * @test **/
	public function it_removes_an_existing_calendar()
	{

		$tmp = factory( App\ApiCalendar::class, 1 )->create(['id' => 999, 'user_id' => $this->client->id]);
		//!Kint::dump($tmp); die();
		
		$res = $this->_object->remove(999, $this->client->id);

		$this->assertEquals($res, true);

	}

	/** * @test **/
	public function it_gets_calendar_by_id_and_user_id()
	{

		$tmp = factory( App\ApiCalendar::class, 1 )->create(['id' => 999, 'user_id' => $this->client->id]);

		$C = $this->_object->get(999, $this->client->id);

		$this->assertEquals($C->id, 999);
		
		//fail if wrong user
		$C = $this->_object->get(999, 88);

		$this->assertFalse($C);
		
	}

	/** * @test **/
	public function it_checks_that_calendar_id_is_owned_by_user_id()
	{
		
		$tmp = factory( App\ApiCalendar::class, 1 )->create(['id' => 999, 'user_id' => 99]);

		$res = $this->_object->userOwnsCalendar(999,99);

		$this->assertTrue($res);

		$res = $this->_object->userOwnsCalendar(999,1);

		$this->assertFalse($res);

	}

	/** * @test **/
	public function it_validates_json_for_a_calendar()
	{

		$this->assertTrue( $this->_object->isValidJson($this->_data)  );

		$this->assertFalse( $this->_object->isValidJson('{"what is this"}')  );
		
	}


}

?>
