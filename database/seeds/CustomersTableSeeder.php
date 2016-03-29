<?php

use Illuminate\Database\Seeder;

class CustomersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		for( $i = 0; $i < 50; $i++ ){
			DB::table('customers')->insert([
				'name' => str_random(15)
			]);
		}
    }
}
