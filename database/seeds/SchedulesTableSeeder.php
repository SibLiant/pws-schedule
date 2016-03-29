<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SchedulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

		for( $i = 0; $i < 200; $i++ ){
			$dateSeed = rand( 0, 30 );
			$carbonDate = Carbon::now()->addDays($dateSeed)->toDateString();
			DB::table('schedules')->insert([
				'job_length_days' => rand(1, 5),
				'worker_id' => rand( 1, 5 ),
				'project_id' => rand( 1, 50 ),
				'scheduled_date' => $carbonDate
			]);
		}
    }
}
