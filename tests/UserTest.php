<?php

//use Illuminate\Foundation\Testing\WithoutMiddleware;
//use Illuminate\Foundation\Testing\DatabaseMigrations;
//use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTest extends TestCase
{

    /**
     * A basic functional test example.
     *
     * @return void
     * @group	unit
     */
    public function testGUID()
    {
			$user = new App\User();
			$guid = $user->generteGUID();
			$this->assertEquals(36, strlen($guid) );
    }

}
