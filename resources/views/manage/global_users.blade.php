@extends('layouts.manage')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">

<a href="/manage" >back to manage index</a>

		<h1>Global Manage Users</h1>

		
		
<table class="table  table-striped users">
		<tr>
			<th>name</th>
			<th>email</th>
			<th>created</th>
			<th>actions</th>
		</tr>
		

    @foreach ($users as $user)
<tr>
<td class="name">{{ $user->name }}</td>
<td class="email">{{ $user->email }}</td>
<td class="created">{{ $user->created_at }}</td>
<td></td>
</tr>
    @endforeach
	</table>

	{!! $users->render() !!}




        </div>
    </div>
</div>

@endsection
