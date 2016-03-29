<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		 //$this->call(CustomersTableSeeder::class);
		 //$this->call(ProjectsTableSeeder::class);
		 //$this->call(WorkersTableSeeder::class);
		 //$this->call(SchedulesTableSeeder::class);


		//how many of each table to generate 
		$genCount = [
			'customers' => 50,
			'projects' => 200,
			'workers' => 5,
			'schedules' => 200,
			'tags' => 400
		];


		//customers
		for( $i = 0; $i < $genCount['customers']; $i++ ){
			DB::table('customers')->insert([
				'name' => str_random(15)
			]);
		}

		//staic customer for testing
		DB::table('customers')->insert([
			'name' => "Mr. Parker Bradtmiller"
		]);

		//projects
		for( $i = 0; $i < $genCount['projects']; $i++ ){
			DB::table('projects')->insert([
				'customer_id' => rand( 1, 50 )
			]);
		}

	


		//static project fortesting
		DB::table('projects')->insert([
			'customer_id' => $genCount['customers'] +1
		]);

		//static worker 0
		DB::table('workers')->insert([
			'name' => "Peters"
		]);

		//workers 
		for( $i = 1; $i < $genCount['workers']; $i++ ){
			DB::table('workers')->insert([
				'name' => str_random(15)
			]);
		}



		//schedules
		for( $i = 0; $i < $genCount['schedules']; $i++ ){
			$dateSeed = rand( 0, 30 );
			$carbonDate = Carbon::now()->addDays($dateSeed)->toDateString();
			DB::table('schedules')->insert([
				'job_length_days' => rand(1, 5),
				'worker_id' => rand( 1, 5 ),
				'user_id' => 1,
				'project_id' => rand( 1, 50 ),
				'scheduled_date' => $carbonDate
			]);
		}


		DB::table('schedules')->insert([
			'job_length_days' => 3,
			'user_id' => 1,
			'worker_id' => 1,
			'project_id' => $genCount['projects'] +1,
			'scheduled_date' => Carbon::now()->toDateString()
		]);

		//tags
			DB::table('tags')->insert([ 'name' => 'guitar box', 'abbreviation'=>'GB', 'background_color' => 'blue', 'border_color'=>'grey', 'tool_tip' => 'testing tool tip' ]);
			DB::table('tags')->insert([ 'name' => 'drump box', 'abbreviation'=>'PB', 'background_color' => 'orange', 'border_color'=>'black' , 'tool_tip' => 'testing tool tip']);
			DB::table('tags')->insert([ 'name' => 'piano box', 'abbreviation'=>'XXL', 'background_color' => 'silver', 'border_color'=>'yellow', 'tool_tip' => 'testing tool tip' ]);
			DB::table('tags')->insert([ 'name' => 'speaker box', 'abbreviation'=>'HVHC', 'background_color' => 'green', 'border_color'=>'red', 'tool_tip' => 'testing tool tip' ]);

		// tags join schedules
		for( $i = 1; $i < $genCount['schedules'] + 1; $i++ ){
			DB::table('schedule_tag')->insert([ 'schedule_id' => $i, 'tag_id' => 1 ]);
			DB::table('schedule_tag')->insert([ 'schedule_id' => $i,  'tag_id' => 2 ]);
			DB::table('schedule_tag')->insert([ 'schedule_id' => $i,  'tag_id' => 3 ]);
			DB::table('schedule_tag')->insert([ 'schedule_id' => $i,  'tag_id' => 4 ]);
		}


		//user
		DB::table('users')->insert([
			'name' => str_random(10),
			'email' => 'pws@gmail.com',
			'password' => bcrypt('1234'),
		]);
    }
}
