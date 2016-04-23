@extends('layouts.app')

@section('content')



<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">



			<div id="splash-container">

			<a href="http://v1.parkerws.com">
			<img src="img/pws_logo.png" id="pws-logo" alt="" height="" width="">
			</a>

					<h3>PWS Scheduler</h3>
			<br/>
			<br/>
			<br/>
			<br/>

<div class="list-group">
  <a href="/RO/calendar/1" class="list-group-item active">
	Main Calendar
  </a>

  </a>
  <a href="/RO/postedSchedule" class="list-group-item">API Posted</a>
</div>


			</div>
		</div>
	</div>
</div>




@endsection
@if ( Config::get('app.debug') )
  <script type="text/javascript">
    document.write('<script src="//localhost:35729/livereload.js?snipver=1" type="text/javascript"><\/script>')
  </script> 
@endif
