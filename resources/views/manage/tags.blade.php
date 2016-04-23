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
<h1 >Tag Index</h1>

</br>
<a href="/manage/tags/add" class="btn btn-primary">Add New Tag</a>
</br>
</br>


@if ($tag)
<table class="table  table-striped">
<thead  class="thead-default">
    <tr>
	
      <th>id</th>
      <th>name</th>
      <th>abbreviation</th>
      <th>background-color</th>
      <th>actions</th>
    </tr>
  </thed>
  <tbody>


@foreach ($tag as $w)

	<tr>
	  <td>{{$w->id}}</td>
	  <td>{{$w['tag_json']['name']}}</td>
	  <td>{{$w['tag_json']['abbreviation']}}</td>
	  <td>{{$w['tag_json']['background_color']}}</td>
	  <td>
		<a href="/manage/tags/edit/{{$w['id']}}" class="btn btn-primary btn-xs">Edit</a>		
		<a href="#myModal"  data-toggle="modal" data-href="/manage/tags/remove/{{$w['id']}}" data-name="{{$w['tag_json']['name']}}" class="btn btn-primary btn-xs confirm-remove" >Remove</a>		
	  </td>
	</tr>

@endforeach
  </tbody>
</table>

@else
<p>We have not created any tags yet.</p>
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
		$(this).find('.modal-body').html('<p>You are about to remove the "'+name+'" tag.</p>');
		$(this).find('#btn-remove').attr('href', $(e.relatedTarget).data('href'));
	});
});

</script>

@endsection
