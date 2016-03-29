<?php

use Illuminate\Database\Seeder;

class WorkersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

		for( $i = 0; $i < 50; $i++ ){
			DB::table('workers')->insert([
				'name' => str_random(15)
			]);
		}
    }
}
