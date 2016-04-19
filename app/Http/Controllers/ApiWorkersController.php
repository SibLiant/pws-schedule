<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\JWTUtils;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\User;
use App\Lib\JsonValidator;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\JsonResponse;
use \Kint as Kint;
use App\ApiScheduleElement;
use App\Calendar;
use App\ApiWorker;
use App\Exceptions\ApiError;

class ApiWorkersController extends Controller
{


	use \App\Http\Controllers\JWTUtils;

    public function __construct()
    {

		$this->middleware('jwt.auth');

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		!Kint::dump($this); die();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function add()
    {

		$workerJson = Input::get('workerJson');

		$user = $this->getAuthenticatedUser();

		if ( ! ApiWorker::isValidJson($workerJson)  ) {

			return new ApiError(500,'worker add error','worker json is not in valid form or missing required elements'  );

		}

		

		if ( $insertId = ApiWorker::add( $user->id, $workerJson  )) {

			$respData =['data' => ['type' => 'ApiWorker', 'id' => $insertId ]];

			$Response = new JsonResponse($respData, 201);

			$Response->header('Content-Type', 'application/vnd.api+json');

			return $Response;
		}
		else {
			abort( 500, 'fail' );
		}

    }

    public function remove($workerId)
    {

		if ( ! $workerId = intval($workerId) )
			return new ApiError(500,'worker remove error','worker id is not valid' );

		$user = $this->getAuthenticatedUser();

		if ( ! ApiWorker::remove($user->id, $workerId ) ) {
			return new ApiError(500,'schedule worker error','failed remove operation.'  );
		}

		$respData =['data' => ['type' => 'ApiWorker', 'remove' => 'succeeded' ]];

		$Response = new JsonResponse($respData, 200);

		$Response->header('Content-Type', 'application/vnd.api+json');

		return $Response;
    }

	
	/**
	 *
	 * @return date string
	 */
	public function get($workerId)
	{

		$user = $this->getAuthenticatedUser();

		if ( ! $workerId = intval( $workerId ) )
			return new ApiError(500,'get worker error','invalid worker id'  );

		if ( $Worker = ApiWorker::get($workerId, $user->id) ) {
			//!Kint::dump($Worker); die();

			$respData =['data' => json_decode( $Worker->worker_json )  ];
			//!Kint::dump($respData); die();

			$Response = new JsonResponse($respData, 200);

			$Response->header('Content-Type', 'application/vnd.api+json');

			return $Response;
			
		}

		return new ApiError(500,'worker error','failed to locate worker'  );

		
	}
}
