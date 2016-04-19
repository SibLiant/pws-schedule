<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\User;
use Carbon\Carbon;
use \Kint as Kint;
use App\Lib\JsonValidator;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\JWTUtils;


class JWTAuthController extends Controller
{

	use \App\Http\Controllers\JWTUtils;

	public function __construct()
	{
		// Apply the jwt.auth middleware to all methods in this controller
		// except for the authenticate method. We don't want to prevent
		// the user from retrieving their token if they don't already have it
		$this->middleware('jwt.auth', ['except' => ['authenticate']]);
	}

	public function index()
	{
		// Retrieve all the users in the database and return them
		$users = User::all();
		return $users;
	}


	public function authenticate(Request $request)
	{
		$credentials = $request->only('email', 'password');

		try {
			// verify the credentials and create a token for the user

			if (! $token = JWTAuth::attempt($credentials)) {
				return response()->json(['error' => 'invalid_credentials'], 401);
			} } catch (JWTException $e) {
			// something went wrong
			return response()->json(['error' => 'could_not_create_token'], 500);
		}

		// if no errors are encountered we can return a JWT
		$respData = ['data' => ['status' => 201, 'token'=>$token]];
		return response()->json($respData, 201);
	}

	
	/**
	 *
	 * @return response 
	 */
	public function postJSON()
	{

		$user = $this->getAuthenticatedUser();

		$json = Input::get('jsonPayload');

		if ( ! JsonValidator::isValid($json) ) {
			return response()->json(['response' => 'invalid json'], 500); 
		}
	
		\DB::table('api_full_post')->insert([
			'user_id' => $user->id,
			'post_json' => $json,
		]);

		return response()->json(['response' => 'success'], 200); 
	}
}
