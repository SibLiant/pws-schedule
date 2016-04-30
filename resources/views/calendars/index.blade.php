@extends('layouts.manage')

@section('content')



<div class="container cnt-manage">
<div class="row">
<div class="col-md-8" >


<h1 >Schedules</h1>


@if ( ! $cals->isEmpty())
<ul class="list-group">

@foreach ($cals as $c)

  <li class="list-group-item">
    <span class="badge"> Records: {{ $schRecCounts[$c->id] }}</span>
	<a href="/RO/calendar/{{ $c->id }}" > {{ $c->calendar_json['name'] }}</a>
  </li>

@endforeach
</ul>

@else
<p>We have not created any schedules yet.</p>
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
});

</script>

@endsection
