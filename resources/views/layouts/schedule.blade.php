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


</head>

<body>


<!-- #############   fixed header   ###################### -->	
<div id="fixed-header">
	<div id="cnt-ctrl">

<div class="col-md-8 selected-col-cnt" id="hdr-proj-col">
			<ul id="selected-project">
			</ul>
			<ul class="project-controls list-inline" id="project-controls">

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

</div> 
<ul class="list-inline" id="schedule-ctrl">
<li> <a href="#cal-ctrl"  data-toggle="modal" data-href="" data-name="add" class="btn btn-success btn" >Add</a></li>
<li> <a href="#cal-ctrl"  id="btn-filter-workers" data-toggle="modal" data-href="" data-name="filter-workers" class="btn btn-success btn" >Filter Workers</a></li>
<li> <a href="#cal-ctrl"  id="btn-filter-tags" data-toggle="modal" data-href="" data-name="filter-tags" class="btn btn-success btn" >Filter Tags</a></li>
</ul>
</div>	

  <!-- Modal project controls -->
  <div class="modal fade modal-lg" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
          <h4 class="modal-title"></h4>
        </div>
        <div class="modal-body">
			<p> empty </p>
        </div>
        <div class="modal-footer">
		  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  <a href="#" class="btn btn-primary" id="btn-modal-submit" >Submit</a>		
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->



  <!-- Modal calendar controls -->
  <div class="modal fade modal-lg" id="cal-ctrl" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
          <h4 class="modal-title"></h4>
        </div>
        <div class="modal-body">
			<p> calendar control </p>
        </div>
        <div class="modal-footer">
		  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  <a href="#" class="btn btn-primary" id="btn-modal-cal-ctrl-submit" >Submit</a>		
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->


  <!-- Modal calendar controls -->
  <div class="modal fade modal-lg" id="tags" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
          <h4 class="modal-title"> Edit Tags</h4>
        </div>
        <div class="modal-body">
			<p> tags control </p>
        </div>
        <div class="modal-footer">
		  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->


<script src="/js/jquery-2.2.0.min.js"></script>
<script src="/js/moment.2.11.1.js"></script>
<script src="/js/underscore_1.8.3.js"></script>
<script src="/js/pwsapp.js"></script>
<script src="/js/jquery-ui-1.11.4.custom/jquery-ui.min.js"></script>
<script src="/js/js-cookie-2.1.1.js"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

<script type="text/javascript" charset="utf-8">
	var scheduleJson = <?php echo $json_data;  ?>;
	var crfs = '{{ csrf_token()  }}';
	PWSCore =  new PWSSchedule.core(scheduleJson);
	PWSCore.render =  new PWSSchedule.render( PWSCore );
	PWSCore.render.bldGridHeader('.hdr-row');
	PWSCore.render.initWorkerDivs('#calendar-workers-scrollable');
	PWSCore.render.initScheduleRecords();
	PWSCore.render.ctrlBind();
	PWSCore.render.applyFilters();
</script>

</body>
</html>



