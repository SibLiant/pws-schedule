<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Faker\Factory as Faker;

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
			'workers' => 8,
			'schedules' => 200,
			'tags' => 400,
			'api_schedule_elements' => 200
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






		$insertArr = [
			'job_length_days' => 3,
			'user_id' => 1,
			'worker_id' => 1,
			'project_id' => $genCount['projects'] +1,
			'scheduled_date' => Carbon::now()->toDateString()
		];
		DB::table('schedules')->insert($insertArr);

		//tags
			DB::table('tags')->insert([ 'name' => 'guitar box', 'abbreviation'=>'GB', 'background_color' => 'blue', 'border_color'=>'grey', 'tool_tip' => 'testing tool tip' ]);
			DB::table('tags')->insert([ 'name' => 'drump box', 'abbreviation'=>'PB', 'background_color' => 'orange', 'border_color'=>'black' , 'tool_tip' => 'testing tool tip']);
			DB::table('tags')->insert([ 'name' => 'piano box', 'abbreviation'=>'XXL', 'background_color' => 'silver', 'border_color'=>'yellow', 'tool_tip' => 'testing tool tip' ]);
			DB::table('tags')->insert([ 'name' => 'speaker box', 'abbreviation'=>'HVHC', 'background_color' => 'green', 'border_color'=>'red', 'tool_tip' => 'testing tool tip' ]);

		//user
		DB::table('users')->insert([ 'name' => 'parker', 'email' => 'pws@gmail.com', 'password' => bcrypt('1234'), 'global_admin' => true, ]);
		DB::table('users')->insert([ 'name' => 'parker_two', 'email' => 'pwbradtmiller@gmail.com', 'password' => bcrypt('1234'), 'global_admin' => false, ]);
		DB::table('users')->insert([ 'name' => 'parker_three', 'email' => 'pwbradtmiller@yahoo.com', 'password' => bcrypt('1234'), 'global_admin' => false, ]);

		$tmp = factory( App\User::class, 10 )->create();

		//calendars
		DB::table('calendars')->insert([
			'name' => 'pws test calendar name',
			'user_id' => 1,
		]);



		//full posts
		DB::table('api_full_posts')->insert([
			'calendar_id' => 1,
			'user_id' => 1,
			'json' => '{"auth":{"username":"parker","key":"asdfifeilsdfkjlkjsdf"},"calendarRange":{"start":"2016-03-29","end":"2016-04-28"},"scheduleRecords":{"1":{"worker_name":"YlRtlX8WuX8VloT","customer_name":"q9ve9MO3iJf1TR4","project_id":9,"customer_id":21,"worker_id":3,"schedule_id":1,"scheduled_date":"2016-04-10","job_length_days":1,"schedule_note":null,"external_link":null,"tags":[4,3,2,1]}},"settings":{"navForward":"30","navBackward":"30"},"workerRecords":[{"worker_id":1,"worker_name":"Peters"},{"worker_id":2,"worker_name":"eftC9Pban1nS6ty"},{"worker_id":3,"worker_name":"YlRtlX8WuX8VloT"},{"worker_id":4,"worker_name":"46q9wXh6d6Rypt9"},{"worker_id":5,"worker_name":"Q5qkaeLcBGgMrqj"}],"tags":{"1":{"id":1,"name":"guitar box","abbreviation":"GB","tool_tip":"testing tool tip","background_color":"blue","border_color":"grey"},"2":{"id":2,"name":"drump box","abbreviation":"PB","tool_tip":"testing tool tip","background_color":"orange","border_color":"black"},"3":{"id":3,"name":"piano box","abbreviation":"XXL","tool_tip":"testing tool tip","background_color":"silver","border_color":"yellow"},"4":{"id":4,"name":"speaker box","abbreviation":"HVHC","tool_tip":"testing tool tip","background_color":"green","border_color":"red"}}}'
		]);

		//schedule elements
		$faker = Faker::create();
		$schedule_id = 500;
			
			
		for( $i = 0; $i < $genCount['api_schedule_elements']; $i++ ){

			$tgs = [];
			
			for( $x = 0; $x < 6; $x++ ){ array_push($tgs, rand(1, 4));  } 

			$jobLength = rand(1, 5);

			$dateSeed = rand( 0, 30 );

			$cDate = Carbon::now()->addDays($dateSeed);

			$carbonEndDate = $cDate->copy()->addDays($jobLength);

			$schedule_id += 1;

			$item = [
				'job_length_days' => $jobLength,
				'worker_id' => rand( 1, 8 ),
				'schedule_id' => $schedule_id,
				'user_id' => 1,
				'customer_name' => $faker->name,
				'project_id' => rand( 1, 50 ),
				'scheduled_date' => $cDate->toDateString(),
				'scheduled_end_date' => $carbonEndDate->toDateString(),
				'tags' => $tgs
			];

			DB::table('api_schedules')->insert([
				'calendar_id' => 1,
				'user_id' => 1,
				'json_data' => json_encode($item)
			]);
		}

		//api_workers
		DB::table('api_workers')->insert([  'user_id' => 1, 'worker_json' => '{"worker_id":1,"external_worker_id":1, "worker_name":"Mark"}']);
		DB::table('api_workers')->insert([  'user_id' => 1, 'worker_json' => '{"worker_id":2,"external_worker_id":2, "worker_name":"Jason"}']);
		DB::table('api_workers')->insert([  'user_id' => 1, 'worker_json' => '{"worker_id":3,"external_worker_id":3, "worker_name":"Isacc"}']);
		DB::table('api_workers')->insert([  'user_id' => 1, 'worker_json' => '{"worker_id":4,"external_worker_id":4, "worker_name":"Wilson"}']);
		DB::table('api_workers')->insert([  'user_id' => 1, 'worker_json' => '{"worker_id":5,"external_worker_id":5, "worker_name":"James"}']);
		DB::table('api_workers')->insert([  'user_id' => 1, 'worker_json' => '{"worker_id":6,"external_worker_id":5, "worker_name":"Alex"}']);
		DB::table('api_workers')->insert([  'user_id' => 1, 'worker_json' => '{"worker_id":7,"external_worker_id":5, "worker_name":"Jordan"}']);
		DB::table('api_workers')->insert([  'user_id' => 1, 'worker_json' => '{"worker_id":8,"external_worker_id":5, "worker_name":"Peter"}']);
		DB::table('api_workers')->insert([  'user_id' => 1, 'worker_json' => '{"worker_id":9,"external_worker_id":5, "worker_name":"Adam"}']);

		//api_calendars
		DB::table('api_calendars')->insert([  'user_id' => 1, 'calendar_json' => '{"id":1,"external_worker_id":1, "name":"hvhc"}']);
		DB::table('api_calendars')->insert([  'user_id' => 1, 'calendar_json' => '{"id":2,"external_worker_id":2, "name":"service"}']);
		DB::table('api_calendars')->insert([  'user_id' => 1, 'calendar_json' => '{"id":3,"external_worker_id":3, "name":"energy"}']);
		DB::table('api_calendars')->insert([  'user_id' => 1, 'calendar_json' => '{"id":4,"external_worker_id":4, "name":"windows"}']);
		DB::table('api_calendars')->insert([  'user_id' => 1, 'calendar_json' => '{"id":5,"external_worker_id":5, "name":"shutters"}']);

		//api_calendar_worker_join
		DB::table('api_calendar_worker_joins')->insert([  'calendar_id' => 1, 'worker_id' => 1]);
		DB::table('api_calendar_worker_joins')->insert([  'calendar_id' => 1, 'worker_id' => 2]);
		DB::table('api_calendar_worker_joins')->insert([  'calendar_id' => 1, 'worker_id' => 3]);
		DB::table('api_calendar_worker_joins')->insert([  'calendar_id' => 1, 'worker_id' => 4]);
		DB::table('api_calendar_worker_joins')->insert([  'calendar_id' => 1, 'worker_id' => 5]);
		DB::table('api_calendar_worker_joins')->insert([  'calendar_id' => 1, 'worker_id' => 6]);
		DB::table('api_calendar_worker_joins')->insert([  'calendar_id' => 1, 'worker_id' => 7]);
		DB::table('api_calendar_worker_joins')->insert([  'calendar_id' => 1, 'worker_id' => 8]);

		//api tags
		DB::table('api_tags')->insert([ 'user_id' => 1,  'tag_json' => '{"schedule_id":1,"tag_id":1,"name":"funkey","background_color":"green","border_color":"blue","abbreviation":"XEDCV","tool_tip":"now is the time for all good men to come to the aid of the country"}']);
		DB::table('api_tags')->insert([ 'user_id' => 1, 'tag_json' => '{"schedule_id":2,"tag_id":2,"name":"coal","background_color":"red","border_color":"blue","abbreviation":"XEDCV","tool_tip":"never give up"}']);
		DB::table('api_tags')->insert([  'user_id' => 1,'tag_json' => '{"schedule_id":3,"tag_id":3,"name":"medina","background_color":"orange","border_color":"blue","abbreviation":"XEDCV","tool_tip":"coding is an emotional roller coaster"}']);
		DB::table('api_tags')->insert([  'user_id' => 1,'tag_json' => '{"schedule_id":4,"tag_id":4,"name":"vanialla","background_color":"blue","border_color":"blue","abbreviation":"XEDCV","tool_tip":"linux is hard but worth it"}']);

		// calendar invitations
		$tmp = factory( App\CalendarInvitation::class, 5 )->create();

    }
}
