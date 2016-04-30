<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|Factories
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

use Carbon\Carbon;

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Calendar::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'user_id' => 2,
        'default' => true
    ];
});


$factory->define(App\ApiSchedule::class, function (Faker\Generator $faker) {

	$dateSeed = rand( 0, 30 );
	$carbonDate = Carbon::now()->addDays($dateSeed)->toDateString();
	$item = [
		'job_length_days' => rand(1, 5),
		'worker_id' => rand( 1, 5 ),
		'user_id' => 1,
		'project_id' => rand( 1, 50 ),
		'schedule_id' => 1234,
		'scheduled_date' => $carbonDate,
		'customer_name' => 'factory_test'
	];

    return [
		'calendar_id' => 1,
		'user_id' => 1,
		'json_data' => json_encode($item)
    ];
});




$factory->define(App\ApiSchedule::class, function (Faker\Generator $faker) {

	$dateSeed = rand( 0, 30 );
	$carbonDate = Carbon::now()->addDays($dateSeed)->toDateString();
	$item = [
		'job_length_days' => rand(1, 5),
		'worker_id' => rand( 1, 5 ),
		'user_id' => 1,
		'project_id' => rand( 1, 50 ),
		'schedule_id' => 1234,
		'scheduled_date' => $carbonDate,
		'customer_name' => 'factory_test'
	];

    return [
		'calendar_id' => 1,
		'user_id' => 1,
		'json_data' => json_encode($item)
    ];
});

$factory->define(App\ApiCalendar::class, function (Faker\Generator $faker) {

	$item = [
		'setting_1' => false,
		'setting_2' => true,
	];

    return [
		'user_id' => 1,
		'calendar_json' => json_encode($item)
    ];
});

$factory->define(App\ApiWorker::class, function (Faker\Generator $faker) {

    return [
		'user_id' => 1,
		'worker_json' => '{"name":"worker name","external_link":"google.com"}'
    ];
});

$factory->define(App\ApiCalendarWorkerJoin::class, function (Faker\Generator $faker) {

    return [
		'worker_id' => 1,
		'calendar_id' => 1
    ];
	
});


$factory->define(App\ApiTag::class, function (Faker\Generator $faker) {

    return [
		'user_id' => 1,
		'tag_json' => '{"name":"tag from test suit","abbreviation":"RREDX","background_color":"blue"}'
    ];
	
});

$factory->define(App\CalendarInvitation::class, function (Faker\Generator $faker) {

    return [
		'invited_by_user_id' => 1,
		'calendar_id' => 1,
		'calendar_admin' => false,
		'url_key' => \App\User::generteGUID(),
		'email' => $faker->email,
    ];
	
});



