@extends('layouts.manage')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">

<a href="/manage" >back to manage index</a>

		<h1>Schedule Invitations</h1>

</br>


@if ( $cals )
<div class="container col-md-12">
<h3>Add New Invitations</h3>

	<div class="container col-md-4 ">
		<table class="table table-striped">
				<tr class="info">
			<th>schedule name</th>
			<th>action </th>
		</tr>
		@foreach ($cals as $c)
		<tr>
			<td>{{ $c->calendar_json['name'] }}</td>
			<td>
				<a href="/manage/calendar-invitations/add/{{$c->id}}" class="btn btn-primary btn-xs">Invite</a>		
			</td>
		</tr>
		@endforeach
		</table>
	</div>
</div>

		

<div class="container col-md-12">
<h3>Invitations</h3>

		<table class="table  table-striped users">
				<tr class="info">
					<th>schedule</th>
					<th>email</th>
					<th>registered name</th>
					<th>created</th>
					<th>actions</th>
				</tr>
		@foreach ($invs as $i)

					<tr>
					<td >{{ $calList[$i->calendar_id] }}</td>
					<td >{{ $i->email }}</td>
					<td > </td>
					<td >{{ $i->created_at }}</td>
					<td>

						<a href="/manage/calendar-invitations/remove/{{$i->id}}" class="btn btn-primary btn-xs">Remove</a>		
						<a href="/manage/calendar-invitations/add/" class="btn btn-primary btn-xs">Re-send</a>		
					</td>
					</tr>

		@endforeach

			</table>


</div>

@endif <!-- only output if cals
@endsection


