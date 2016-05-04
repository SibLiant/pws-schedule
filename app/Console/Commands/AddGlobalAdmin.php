<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;

class AddGlobalAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pws:add-global-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'add a global admin to the users table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

		$email = $this->ask('email address');

		$name = $this->ask('what is your name');

		$password = $this->secret('password');

		$user = new User;

		$user->email = $email;

		$user->name = $name;

		$user->password = bcrypt($password);

		$user->global_admin = true;

		$user->account_admin = true;

		$user->save();
    }
}
