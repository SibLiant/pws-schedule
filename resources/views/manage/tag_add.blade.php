@extends('layouts.manage')

@section('content')
<?php //ddd($Calendar); ?>




<div class="container">
    <div class="row">
        <div class="col-md-6" >

@if (count($errors) > 0)
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


{!! Form::model($tag, array('route' => array('manage.tag.add', $tag->id))) !!}


<legend> Add Tag </legend> 

@foreach (  $tag['jsonDefaults'] as $f => $v )

	<div class="form-group">
		<label for="{{$f}}">{{$f}}</label>
		<input class="form-control" name="{{$f}}" type="text" id="{{$f}}" value="{{$v}}">
	</div>

@endforeach



<div class="form-group">
	<a href="/manage/tags" class="btn btn-default">Cancel</a>		
	{!! Form::submit('Submit', ['class' => 'btn btn-primary']) !!}
</div>


{!! Form::close() !!}


    </div>
</div>
@endsection
