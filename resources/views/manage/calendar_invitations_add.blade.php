@extends('layouts.manage')

@section('content')
<?php //ddd($Calendar); ?>




<div class="container">
    <div class="row">
        <div class="col-md-6" >

<a href="/manage/calendar-invitations" >back to invitations index</a>
</br>
</br>
</br>
@if (count($errors) > 0)
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


{!! Form::open(array('route' => array('manage.calendar-invitation.add'))) !!}


<legend>Calendar Invitation</legend> 

@if ( $errors && Session::has('_old_input') )

	<?php $old = Session::get('_old_input'); ?>	
		<div class="form-group">
			<label for="email">email</label>
			<input class="form-control" name="email" type="text"  value="{{$old['email'] or ''}}">
		</div>

@else

	<div class="form-group">
		<label for="email">email</label>
		<input class="form-control" name="email" type="text"  value="">
	</div>


@endif









<div class="form-group">
	<a href="/manage/calendar-invitations" class="btn btn-default">Cancel</a>		
	{!! Form::submit('Submit', ['class' => 'btn btn-primary']) !!}
</div>


{!! Form::close() !!}


    </div>
</div>
@endsection
