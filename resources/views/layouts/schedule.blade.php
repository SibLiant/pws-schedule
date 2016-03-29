<!DOCTYPE html>
<html>
<head>
		<title>PWS Schedule</title>



		<!-- Styles -->
		<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
		{{-- <link href="{{ elixir('css/app.css') }}" rel="stylesheet"> --}}

		<style>
			body {
				font-family: 'Lato';
			}

			.fa-btn {
				margin-right: 6px;
			}
		</style>


		<link rel="stylesheet" type="text/css" href="/css/app.css"> 
<!--
		<link href="https://cdn.rawgit.com/mochajs/mocha/2.2.5/mocha.css" rel="stylesheet" />
-->


		<script src="/js/jquery-2.2.0.min.js"></script>
		<script src="/js/moment.2.11.1.js"></script>
		<script src="/js/underscore_1.8.3.js"></script>
		<script src="/js/pwsapp.js"></script>



<!--
		<script src="https://cdn.rawgit.com/mochajs/mocha/2.2.5/mocha.js"></script>
  		<script src="http://chaijs.com/chai.js"></script>

-->
</head>

<body>

@if (Session::has('flash_notification.message'))
    <div class="alert alert-{{ Session::get('flash_notification.level') }}">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        {{ Session::get('flash_notification.message') }}
    </div>
@endif

<!-- #############   fixed header   ###################### -->	
<div id="fixed-header">
	<div id="cnt-ctrl">

	<ul id="selected-project">
	</ul>
</div>

<!-- #############   calendar header   ###################### -->	
	<div id="cnt-scroll-header">
							<div id="cnt-calendar-header">
							<div class="hdr-row" id="hdr-row">
							</div> <!-- cnt-calendar-header -->	
							</div>
	</div>
</div>
<!-- #############   end fixed header   ###################### -->	


<!-- #############    begin worker grids   ###################### -->	
<div id="cnt-worker-grids"> 
 <div id="calendar-workers-scrollable"> </div> <!-- calendar-worker-scrollable -->	
</div>
<div id="cnt-footer"> 
<div id="selected-project"> 
project

</div> container footer </div>	


<div id="mocha"></div>
<div id="messages"></div>
<div id="fixtures"></div>



<script type="text/javascript" charset="utf-8">
	var scheduleJson = <?php echo $json_data;  ?>;
	PWSCore =  new PWSSchedule.core(scheduleJson);
	PWSCore.render =  new PWSSchedule.render( PWSCore );
	PWSCore.render.bldGridHeader('.hdr-row');
	PWSCore.render.initWorkerDivs('#calendar-workers-scrollable');
	PWSCore.render.initScheduleRecords();
	PWSCore.render.ctrlBind();
</script>




<script>//mocha.setup('bdd')</script>
<script>//mocha.run();</script>
</body>
</html>



