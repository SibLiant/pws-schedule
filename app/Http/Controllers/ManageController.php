<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repos;
use Illuminate\Support\Facades\Input;
use App\Exceptions\Handler;
use App\Calendar;
use App\Worker;
use App\ApiCalendarWorkerJoin;
use App\Tag;
use Kint;
use Carbon\Carbon;
use View;
use Flash;

/*
 * todo: pagination
 * todo: lots of duplicate code on the views and in these controllers
 */
class ManageController extends Controller
{

	private $calendar;
	private $worker;
	private $tag;
	private $validationRulesCalendar = [
		'name' => 'required|max:30'
	];

	private $validationRulesWorker= [
		'worker_name' => 'required|max:30'
	];

	private $validationRulesTag= [
		'name' => 'required|max:30',
		'background_color' => 'required|max:20',
		'abbreviation' => 'required|max:5'
	];


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

		$this->middleware('auth');

		$this->calendar = new \App\ApiCalendar;

		$this->worker = new \App\ApiWorker;

		$this->tag = new \App\ApiTag;

    }

	
	/**
	 *
	 */
	public function index()
	{


		return View::make('manage.index');
		
	}

	
	/**
	 *
	 */
	public function calendars()
	{

		return View::make('manage.calendars', array(
			'cal' => $this->calendar->where('user_id', Auth::user()->id)->get()->toArray()
			)
		);
		
	}

	
	/**
	 *
	 */
	public function calendarsAdd(Request $request)
	{

		if ($request->isMethod('post'))
		{
			$this->validate($request, $this->validationRulesCalendar);

			$recs = Input::all();

			unset($recs['_token']);


			$result = $this->calendar->add(Auth::user()->id, json_encode($recs));

			if ( $result ) {

				\Flash::success('Calendar Added');

				return redirect()->route('manage.calendar.index');

			} 

			
			\Flash::error('Error trying to add a new calendar');

			return redirect()->route('manage.calendar.index');
			
		}

		return View::make('manage.calendar_add', array('cal' => $this->calendar));
		
	}

	
	/**
	 *
	 */
	public function calendarsRemove($calendarId)
	{

		$cal = $this->calendar->find($calendarId);

		if ( ! $cal || ! $cal->user_id === Auth::user()->id) {
			
			\Flash::warning('can not locate that calendar id for deletion');

			return redirect()->route('manage.calendar.index');

		}

		if (  $cal->delete() ) {

			\Flash::success("Calendar {$cal->calendar_json['name']} deleted");

			return redirect()->route('manage.calendar.index');
		}
	}

	
	/**
	 *
	 */
	public function calendarsEdit(Request $request, $calendarId)
	{

		if ($request->isMethod('post')) {
			
			$this->validate($request, $this->validationRulesCalendar);

			$recs = Input::all();

			$result = $this->calendar->where('id', $calendarId)->where('user_id', Auth::user()->id)
				->update(['calendar_json' => json_encode($recs) ]);
			

			if ( $result ) {

				\Flash::success('Calendar Edited');

				return redirect()->route('manage.calendar.index');

			} 
		}

		$cal = $this->calendar->find($calendarId);

		if ( ! $cal || ! $cal->user_id === Auth::user()->id  ) {

			\Flash::warning('can not locate that calendar id for edit');

			return redirect()->route('manage.calendar.index');
		}


		return View::make('manage.calendar_edit', array('cal' => $cal));
		
	}

	
	/**
	 *
	 */
	public function workers()
	{
		
		return View::make('manage.workers', array(
			'worker' => $this->worker->where('user_id', Auth::user()->id)->get()->toArray()
			)
		);

	}

	
	/**
	 *
	 */
	public function workersAdd(Request $request)
	{

		if ($request->isMethod('post'))
		{
			$this->validate($request, $this->validationRulesWorker);

			$recs = Input::all();

			unset($recs['_token']);


			$result = $this->worker->add(Auth::user()->id, json_encode($recs));

			if ( $result ) {

				\Flash::success('Worker Added');

				return redirect()->route('manage.worker.index');

			} 

			
			\Flash::error('Error trying to add a new worker');

			return redirect()->route('manage.worker.index');
			
		}

		return View::make('manage.worker_add', array('worker' => $this->worker));
		
	}

	
	/**
	 *
	 */
	public function workersEdit(Request $request, $workerId)
	{
		
		if ($request->isMethod('post')) {
			
			$this->validate($request, $this->validationRulesWorker);

			$recs = Input::all();

			$result = $this->worker->where('id', $workerId)->where('user_id', Auth::user()->id)
				->update(['worker_json' => json_encode($recs) ]);
			

			if ( $result ) {

				\Flash::success('Worker Edited');

				return redirect()->route('manage.worker.index');

			} 
		}

		$worker = $this->worker->find($workerId);

		if ( ! $worker || ! $worker->user_id === Auth::user()->id  ) {

			\Flash::warning('can not locate that worker id for edit');

			return redirect()->route('manage.worker.index');
		}


		return View::make('manage.worker_edit', array('worker' => $worker));
	}

	
	/**
	 *
	 */
	public function workersRemove($workerId)
	{
		
		$worker = $this->worker->find($workerId);

		if ( ! $worker || ! $worker->user_id === Auth::user()->id) {
			
			\Flash::warning('can not locate that worker id for deletion');

			return redirect()->route('manage.worker.index');

		}

		if (  $worker->delete() ) {

			\Flash::success("Worker {$worker->worker_json['worker_name']} deleted");

			return redirect()->route('manage.worker.index');
		}
		
	}

	
	/**
	 *
	 */
	public function tags()
	{
		
		return View::make('manage.tags', array(
			'tag' => $this->tag->where('user_id', Auth::user()->id)->get()
			)
		);
	}

	
	/**
	 *
	 */
	public function tagsAdd(Request $request)
	{

		if ($request->isMethod('post'))
		{
			$this->validate($request, $this->validationRulesTag);

			$recs = Input::all();

			unset($recs['_token']);

			$result = $this->tag->add(Auth::user()->id, json_encode($recs));

			if ( $result ) {

				\Flash::success('Tag Added');

				return redirect()->route('manage.tag.index');

			} 

			
			\Flash::error('Error trying to add a new tag');

			return redirect()->route('manage.tag.index');
			
		}

		return View::make('manage.tag_add', array('tag' => $this->tag));
		
	}

	
	/**
	 *
	 */
	public function tagsEdit(Request $request, $tagId)
	{

		if ($request->isMethod('post')) {
			
			$this->validate($request, $this->validationRulesTag);

			$recs = Input::all();

			$result = $this->tag->where('id', $tagId)->where('user_id', Auth::user()->id)
				->update(['tag_json' => json_encode($recs) ]);
			

			if ( $result ) {

				\Flash::success('Tag Edited');

				return redirect()->route('manage.tag.index');

			} 
		}

		$tag = $this->tag->find($tagId);

		if ( ! $tag || ! $tag->user_id === Auth::user()->id  ) {

			\Flash::warning('can not locate that tag id for edit');

			return redirect()->route('manage.tag.index');
		}


		return View::make('manage.tag_edit', array('tag' => $tag));
		
	}

	
	/**
	 *
	 */
	public function tagsRemove($tagId)
	{

		$tag = $this->tag->find($tagId);

		if ( ! $tag || ! $tag->user_id === Auth::user()->id) {
			
			\Flash::warning('can not locate that tag id for deletion');

			return redirect()->route('manage.tag.index');

		}

		if (  $tag->delete() ) {

			\Flash::success("tag {$tag->tag_json['name']} deleted");

			return redirect()->route('manage.tag.index');
		}
		
	}

	
	/**
	 *
	 */
	public function calendarWorkers($calendarId)
	{

		if ( ! $this->calendar->userOwnsCalendar( $calendarId, Auth::user()->id )  ) {
			abort(500, "unknown calendar");
		}

		$recs =  $this->worker->where('user_id', Auth::user()->id)->get()->toArray();

		$workerDrop = [];

		foreach($recs as $r){ 

			$workerDrop[$r['id']] = $r['worker_json']['worker_name'];

		}

		return View::make('manage.calendar_workers', array(
			'workers' => $this->calendar->find($calendarId)->workers()->get(),
			'cal' => $this->calendar->findOrFail($calendarId),
			'workerDrop' => $workerDrop
		));

		
	}

	
	/**
	 *
	 */
	public function calendarWorkersRemove($calendarId, $workerId)
	{


		$userId = Auth::user()->id;

		$cal = $this->calendar->findOrFail($calendarId);

		$worker = $this->worker->findOrFail($workerId);

		if ( $cal->user_id == $userId && $worker->user_id == $userId ) {

			if  ( ApiCalendarWorkerJoin::unJoin($calendarId, $workerId) ) {

				Flash::success('Worker removed');

				return redirect()->route('manage.calendar-workers', $calendarId);
			}

			
		}
		
		Flash::error('Worker remove error!');

		return redirect()->back();
		
	}

	
	/**
	 *
	 */
	public function calendarWorkersAdd($calendarId, $workerId)
	{

		$userId = Auth::user()->id;

		$cal = $this->calendar->findOrFail($calendarId);

		$worker = $this->worker->findOrFail($workerId);

		if ( $cal->user_id == $userId && $worker->user_id == $userId ) {

			if ( ApiCalendarWorkerJoin::join($calendarId, $workerId) ) {

				Flash::success('Worker added');

				return redirect()->back();
				
			}
		}

		abort(403, "error");
	}
}

