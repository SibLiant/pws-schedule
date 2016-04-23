@extends('layouts.manage')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">


					<div class="list-group">
						<a href="/RO/calendar/1" class="list-group-item"> RO Calendar 1 </a>
						<a href="/manage/calendars" class="list-group-item "> Calendars </a>
						<a href="/manage/workers" class="list-group-item">Workers</a>
						<a href="/manage/tags" class="list-group-item">Tags</a>
					</div>


                </div>

            </div>
        </div>
    </div>
</div>

@endsection
