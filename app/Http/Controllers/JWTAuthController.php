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


class JWTAuthController extends Controller
{

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
		return response()->json(compact('token'));
	}


	public function getAuthenticatedUser()
	{
		try {

			if (! $user = JWTAuth::parseToken()->authenticate()) {
				return response()->json(['user_not_found'], 404);
			}

		} catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

			return response()->json(['token_expired'], $e->getStatusCode());

		} catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

			return response()->json(['token_invalid'], $e->getStatusCode());

		} catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

			return response()->json(['token_absent'], $e->getStatusCode());

		}

		// the token is valid and we have found the user via the sub claim
		return $user;
	}


	
	/**
	 *
	 * @return date string
	 */
	//public function clientJSON(Request $request)
	//{
		//$token = JWTAuth::getToken();
		//$user = $this->getAuthenticatedUser();
		//$calendarJson = Input::get('calendarJson');

		//if ( $calendarJson ) {
			//$validator = new JsonValidator( $calendarJson );
			//$validJson = $validator->isValid();
			//return view('ro_page')->with('json_data', $validJson);
		//}
	//}

	
	/**
	 *
	 * @return response 
	 */
	public function postJSON()
	{

		$user = $this->getAuthenticatedUser();

		$json = Input::get('jsonPayload');

		$validator = new JsonValidator( $json );

		$validJson = $validator->isValid();

		if ( ! $validJson ) return response()->json(['error' => 'not valid json'], 500); 

		\DB::table('api_full_post')->insert([
			'user_id' => 1,
			'post_json' => $validJson,
		]);

		return response()->json(['response' => 'success'], 200); 
	}

}
