<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('users')->insert([
            'name' => str_random(10),
            'email' => 'pws@gmail.com',
            'password' => bcrypt('1234'),
        ]);

    }
}
