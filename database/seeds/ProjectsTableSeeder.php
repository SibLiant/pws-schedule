<?php

use Illuminate\Database\Seeder;

class ProjectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		for( $i = 0; $i < 50; $i++ ){
			DB::table('projects')->insert([
				'customer_id' => rand( 1, 50 )
			]);
		}
    }
}
