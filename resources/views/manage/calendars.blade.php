@extends('layouts.manage')

@section('content')
<?php //ddd($Calendar); ?>



<div class="container cnt-manage">
<div class="row">
<div class="col-md-8" >

@if (count($errors) > 0)
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif



<a href="/manage" >back to manage index</a>
</br>
<h1 >Calendar Index</h1>

</br>
<a href="/manage/calendars/add" class="btn btn-primary">Add New Calendar</a>
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


@foreach ($cal as $c)

	<tr>
	  <td>{{$c['calendar_json']['name']}}</td>
	  <td>
		<a href="/manage/calendars/edit/{{$c['id']}}" class="btn btn-primary btn-xs">Edit</a>		
		<a href="#myModal"  data-toggle="modal" data-href="/manage/calendars/remove/{{$c['id']}}" data-name="{{$c['calendar_json']['name']}}" class="btn btn-primary btn-xs confirm-remove" >Remove</a>		
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
		$(this).find('.modal-body').html('<p>You are about to remove the "'+name+'" calendar.</p>');
		$(this).find('#btn-remove').attr('href', $(e.relatedTarget).data('href'));
	});

});

//$('a').click(function(e){
	//e.preventDefault();
	//target = e.target;
	//console.debug(target);
//});

</script>

@endsection
