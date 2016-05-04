@extends('layouts.manage')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-6">
	

<a href="/calendars" >back to schedule index</a>

	<h1>Manage</h1>


	@if ( Auth::user()->isGlobalAdmin() )

	<a href="/manage/global-users" class="list-group-item">manage global users</a>

	@endif

	@if ( Auth::user()->isAccountAdmin() )

					<div class="list-group">
						<a href="/manage/calendar-invitations" class="list-group-item">Invitations</a>
						<a href="/manage/calendars" class="list-group-item "> Calendars </a>
						<a href="/manage/workers" class="list-group-item">Workers</a>
						<a href="/manage/tags" class="list-group-item">Tags</a>
					</div>

	@endif


        </div>
    </div>
</div>

@endsection
