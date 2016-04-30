<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Flash;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/calendars';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {

        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }


	public function showRegistrationForm($urlKey = null)
	{

		//user may have clicked a calendar invitation.  we're running all 
		//invitations through here to catch anyone  not registered yet
		//if the email is already
		//registered just redirect to the calendar index

		if ( $urlKey ) {

			$inv = \App\CalendarInvitation::where('url_key', $urlKey)->first();

			if ( ! $inv ) {

				Flash::error('Invalid key.  Please request new from calendar admin');

				return redirect()->back();
				
			}

			if ( User::emailRegistered($inv->email) ) {

				Flash::success('The schedule will appear in your index');

				return redirect()->route('calendar.index');

			}

			return view('auth.register')->with('inv', $inv);
		}
		
		if (property_exists($this, 'registerView')) {
			return view($this->registerView);
		}

		return view('auth.register');

	}
}
