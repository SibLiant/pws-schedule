<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repos;
use App\Lib\JsonValidator;
use App\Lib\PWS_JSON_Generator;
use Illuminate\Support\Facades\Input;
use App\Exceptions\Handler;

class ReadonlyController extends Controller
{
		Protected $repo;

    //
	
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(\App\Repos\Local_DB_RO_Repository $repo)
    {
		$this->middleware('auth');

		$this->repo = $repo;
    }

	/**
	* undocumented class
	*
	* @package default
	* @subpackage default
	* @author Parker Bradtmiller
	*/
    public function index(Request $request)
    {
		$json = $this->repo->getJSON();

		$validJson = $validator->isValid();

        return view('ro_page')->with('json_data', $validJson);
    }

	/**
	 *
	 * @return date string
	 */
	public function postedSchedule(Request $request)
	{

		$jsonGen = new PWS_JSON_Generator();

		$post = \DB::table('api_full_post')
			//->where('user_id', '=', $user->id)
			->where('user_id', '=', 1)
			->orderBy('id', 'desc')
			->limit(1)
			->get();

		$json = $jsonGen->getJSON();

		if ( $post[0] ) {
			
			return view('ro_page')->with('json_data', $post[0]->post_json);

		}
		else {

			Session::flash('msg-error', 'no json post data found');

			return view('/ro_page');
		}
		
	}

}
