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


<h1>Edit Tag - {{$tag->tag_json['name']}}</h1>
</br>

{!! Form::model($tag, array('route' => array('manage.tag.edit', $tag->id))) !!}



<?php //d($tag['jsonDefaults']); ?>

@if ( $errors && Session::has('_old_input') )

	<?php $old = Session::get('_old_input'); ?>	
	@foreach (  $tag['jsonDefaults'] as $f => $v )
		<div class="form-group">
			<label for="{{$f}}">{{$f}}</label>
			<input class="form-control" name="{{$f}}" type="text" id="{{$f}}" value="{{$old[$f] or ''}}">
		</div>
	@endforeach

@else

	@foreach (  $tag['jsonDefaults'] as $f => $v )
		<div class="form-group">
			<label for="{{$f}}">{{$f}}</label>
			<input class="form-control" name="{{$f}}" type="text" id="{{$f}}" value="{{$tag->tag_json[$f] or ''}}">
		</div>
	@endforeach

@endif

<div class="form-group">
	<a href="/manage/tags" class="btn btn-default">Cancel</a>		
	{!! Form::submit('Submit', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}


    </div>
</div>
@endsection
