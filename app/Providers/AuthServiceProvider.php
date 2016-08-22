<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate  $gate
     * @return void
     */
    public function boot(GateContract $gate)
    {

        $this->registerPolicies($gate);

		$gate->before(function ($user, $ability) {
			//if ($user->isGlobalAdmin()) {
				//return true;
			//}


			//if user owns it
			//if (  $user->id == $cal->user_id) return true;
		});

		$gate->define('calendar-view', function ($user, $cal) {

			$invs = $cal->invitations;

			//if user is invited to it
			foreach($invs as $i) {
				if ( $i->email == $user->email ) return true;
			}

			//if user owns it
			if (  $user->id == $cal->user_id) return true;

			return false;
								
		});

		$gate->define('calendar-edit', function ($user, $cal) {

			$invs = $cal->invitations;

			//if user is invited to it
			foreach($invs as $i) {
				ddd($i);
				if ( $i->email == $user->email ) return true;
			}

			//if user owns it
			if (  $user->id == $cal->user_id) return true;

			return false;
								
		});

    }
}
