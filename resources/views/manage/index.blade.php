@extends('layouts.manage')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-6">


	<h1>User Calendar View</h1>


	@if ( Auth::user()->isGlobalAdmin() )

	<a href="/manage/global-users" class="list-group-item">manage global users</a>

	@endif



	@if ( Auth::user()->isAccountAdmin() )

	<h3>Account Admin</h3>


					<div class="list-group">
						<a href="/manage/calendar-invitations" class="list-group-item">Invitations</a>
						<a href="/RO/calendar/1" class="list-group-item"> RO Calendar 1 </a>
						<a href="/manage/calendars" class="list-group-item "> Calendars </a>
						<a href="/manage/workers" class="list-group-item">Workers</a>
						<a href="/manage/tags" class="list-group-item">Tags</a>
					</div>

	@endif


        </div>
    </div>
</div>

@endsection
