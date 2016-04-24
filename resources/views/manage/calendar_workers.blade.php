@extends('layouts.manage')

@section('content')

<?php //ddd($allWorkers); ?>

<div class="container cnt-manage">
<div class="row">
<div class="col-md-8" >


<a href="/manage" >back to manage index</a>
</br>

<h1>Calendar "{{$cal->calendar_json['name']}}" -- Manage Workers</h1>
</br>

<?php //ddd( Route::current()->parameter('calendarId') ); ?>

<div class="styled-select blue semi-square" id="worker-drop-cnt">
{!! Form::select('worker', $workerDrop, null, ['id' => 'worker-drop']) !!}
</div>
<a href="/manage/workers/add" class="btn btn-primary btn-xs" id="btn-add-worker">Add worker</a>


</br>
</br>


@if ($cal)
<table class="table  table-striped">
<thead  class="thead-default">
    <tr>
      <th>name</th>
      <th>actions</th>
    </tr>
  </thed>
  <tbody>


@foreach ($workers as $w)

<?php //ddd($w->worker_json['worker_name']) ?>

	<tr>
	  <td>{{$w->worker_json['worker_name']}}</td>
	  <td>
		<a href="#myModal"  data-toggle="modal" data-href="/manage/calendar/{{$cal->id}}/remove/worker/{{$w->id}}" data-name="{{$w->worker_json['worker_name']}}" class="btn btn-primary btn-xs confirm-remove" >Remove Worker</a>		
	  </td>
	</tr>

@endforeach
  </tbody>
</table>

@else
<p>We have not created any calendars yet.</p>
@endif








</div>
</div>
</div>





@endsection


@section('manageScripts')



<script type="text/javascript">


$(document).ready(function(){
	$('#myModal').on('show.bs.modal', function (e) {
		name = $(e.relatedTarget).data('name');
		$(this).find('.modal-body').html('<p>You are about to remove the "'+name+'" worker.</p>');
		$(this).find('#btn-remove').attr('href', $(e.relatedTarget).data('href'));
	});

	$('#btn-add-worker').click(function(e){

		e.preventDefault();
		calendarId = {{ Route::current()->parameter('calendarId') }};
		workerId = $('#worker-drop').val();
		window.location = '/manage/calendar/'+calendarId+'/add/worker/'+workerId;
		
	});


});

//$('a').click(function(e){
	//e.preventDefault();
	//target = e.target;
	//console.debug(target);
//});

</script>

@endsection
