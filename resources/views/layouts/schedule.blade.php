<!DOCTYPE html>
<html>
<head>
		<title>PWS Schedule</title>
		<!-- Styles -->

		<link href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.6/darkly/bootstrap.min.css" rel="stylesheet">

		<style>
			body {
				font-family: 'Lato';
			}

			.fa-btn {
				margin-right: 6px;
			}
		</style>


		<link rel="stylesheet" type="text/css" href="/css/app.css"> 

		<script src="/js/jquery-2.2.0.min.js"></script>
		<script src="/js/moment.2.11.1.js"></script>
		<script src="/js/underscore_1.8.3.js"></script>
		<script src="/js/pwsapp.js"></script>

</head>

<body>


<!-- #############   fixed header   ###################### -->	
<div id="fixed-header">
	<div id="cnt-ctrl">

<div class="col-md-8 selected-col-cnt" id="hdr-proj-col">
			<ul id="selected-project">
			</ul>
			<ul class="project-controls list-inline">
				<li> <a href="#" class="btn btn-warning btn-xs">test 1</a></li>
				<li> <a href="#" class="btn btn-info btn-xs">test 1</a></li>
				<li> <a href="#" class="btn btn-info btn-xs">test 1</a></li>
				<li> <a href="#" class="btn btn-info btn-xs">test 1</a></li>
			</ul>
		</div>

<div class="col-md-3" id="hdr-cal-name-col"> <h3 id="calendar-name"></h3>  </div>
<div class="col-md-1" id="hdr-manage-menu-col">


                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i>Logout</a></li>
                                <li><a href="{{ url('/manage') }}"><i class="fa fa-btn fa-asterisk"></i>Manage</a></li>
                            </ul>
                        </li>
                </ul>
</div>




	
</div>

<!-- #############   calendar header   ###################### -->	
	<div id="cnt-scroll-header">
		<div id="cnt-calendar-header">
			<div class="hdr-row" id="hdr-row"> </div> <!-- cnt-calendar-header -->	
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



<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

</body>
</html>



