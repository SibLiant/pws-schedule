@extends('layouts.manage')

@section('content')
<?php //ddd($worker); ?>



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
<h1 >Worker Index</h1>

</br>
<a href="/manage/workers/add" class="btn btn-primary">Add New Worker</a>
</br>
</br>


@if ($worker)
<table class="table  table-striped">
<thead  class="thead-default">
    <tr>
      <th>name</th>
      <th>actions</th>
    </tr>
  </thed>
  <tbody>


@foreach ($worker as $w)

	<tr>
	  <td>{{$w['worker_json']['worker_name']}}</td>
	  <td>
		<a href="/manage/workers/edit/{{$w['id']}}" class="btn btn-primary btn-xs">Edit</a>		
		<a href="#myModal"  data-toggle="modal" data-href="/manage/workers/remove/{{$w['id']}}" data-name="{{$w['worker_json']['worker_name']}}" class="btn btn-primary btn-xs confirm-remove" >Remove</a>		
	  </td>
	</tr>

@endforeach
  </tbody>
</table>

@else
<p>We have not created any workers yet.</p>
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
