<?php 

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\ApiTag;
use Carbon\Carbon;

class ApiTagTest extends TestCase
{

	use DatabaseTransactions;


	private $_object;
	private $_data;
	
	protected function setUp()
	{
			parent::setUp();

			$this->_object = new ApiTag;
	
			$this->_data = '{"name":"tag from test suit","abbreviation":"RREDX","background_color":"blue"}';
	}

	/** * @test **/
	public function it_adds_an_element()
	{


		$res = $this->_object->add(1, $this->_data );

		$this->assertInternalType("int", $res);

	}


	/** * @test **/
	public function it_deletes_an_element_from_the_database_completely()
	{

		//add a record so we can delete it
		$tmp = factory( App\ApiTag::class, 1 )->create(['id' => 33, 'tag_json' => $this->_data, 'user_id' => $this->client->id]);

		$res = $this->_object->remove($this->client->id, 33);

		$this->assertTrue( $res );
		
	}

	/** * @test **/
	public function it_gets_an_element_by_id()
	{
		
		$tmp = factory( App\ApiTag::class, 1 )->create(['id' => 33, 'tag_json' => $this->_data, 'user_id' => $this->client->id]);

		$S = $this->_object->get(33, $this->client->id);

		$this->assertEquals($S->id, 33 );
		
	}



}

?>
