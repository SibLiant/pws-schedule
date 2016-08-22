<?php 

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ScheduleControllerTest extends TestCase
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
	public function whatever()
	{
		
	}


}

?>
