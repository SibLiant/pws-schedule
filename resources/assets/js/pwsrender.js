/*global PWSSchedule, moment */
/*exported  getCalStart, getCalRange, setCalStart */
/* jshint unused:false */
PWSSchedule.render = function( core ){
	"use strict";


	var projectSelected = {};
	var hdrTarget = null;

	var bldGridHeader = function(targetElement){
		//clear any existing elements

		if ( targetElement ){
			hdrTarget = ( targetElement instanceof jQuery ) ? targetElement : $( targetElement );
		}

		var hdrdivs = [];
		$('div.hdr-row').html('');

		//build the controls first
		hdrdivs.push('<div id="name-placeholder"> <a href="#" id="cal-ctrl-back" class="btn"><</a> &nbsp; &nbsp; <a href="#" id="cal-ctrl-forward" class="btn">></a> </div>');
		var i;						

		for(i=0; i < core.config.calRangeInt; i++){
			hdrdivs.push('<div class="cnt-hdr-date">'+ core.config.momCalStart.clone().add(i, 'days').format(core.config.headerDateFormat)+' </div>');
		}
		hdrTarget.append(hdrdivs);
	};

	var initScheduleRecords = function(){
		var rws;
		$.each(core.workers, function( workerIndex, worker ) {
			buildWorkerDays( worker.id );
			rws = worker.buildRows(core.config.momCalStart, core.config.calRangeInt);
			//console.debug(rws);
			$.each(rws, function( rowIndex, row ) {
				//console.debug(row);
				renderWorkerRow( worker.id, row  );
			});
		});
	};

	var reRender = function(){
		bldGridHeader();
		initScheduleRecords();
		ctrlBind();

		var scrollHeader = document.getElementById('cnt-scroll-header');
		var scrollWorkers = document.getElementById('calendar-workers-scrollable');
		var scrollWkGrids = document.getElementById('cnt-worker-grids');
		if ( scrollHeader  ) { scrollHeader.scrollLeft = 0; }
		if ( scrollWorkers ) { scrollWorkers.scrollLeft = 0; }
		if ( scrollWkGrids ) { scrollWkGrids.scrollLeft = 0; }



		//$('#cnt-worker-grids').scroll(function(e) {
				//var pos = $('#cnt-worker-grids').scrollLeft();
				//$(".worker-name, #name-placeholder").css({
						//left: pos
				//});
		//});
	};

	var navCal = function(direction, days){
		if ( core.config.navRootUrl ) { 
			calNavRootUrl(direction); 
		}
		if ( direction === 'forward' ) {
			core.config.momCalStart.add( days, 'days' );
		}

		if ( direction === 'backward' ) {
			core.config.momCalStart.subtract( days, 'days' );
		}
		reRender();
	};

	var calNavRootUrl = function(direction){

		var newTargetDate;
		if ( direction === "forward" ) {
			newTargetDate = core.config.momCalStart.clone().add( core.config.navForward, 'days' );
		}
		else { //backward
			newTargetDate = core.config.momCalStart.clone().subtract( core.config.navBackward, 'days' );
		}

		var url = core.config.navRootUrl + '/' + newTargetDate.format( core.config.pwsDateFormat );
		window.location = url;
		throw new Error( 'Redirecting' );

	};

	var clearWorkerScheduleElements = function(worker_id){
		var momStart = core.config.momCalStart.clone();
		var rng = core.config.calRangeInt;
		for (var i = 0; i < rng; i++) { 
			var select = bldDaySelector(worker_id, momStart.clone().add(i, 'days'));
			$(select).html('');
		}

	};

	var clearWorkerDayDivs = function(worker_id){
		//var select = $('#worker-row_'+worker_id).html('');
		var select = $('#worker-row_'+worker_id).children();
		$.each(select, function( index, value ) {
			if ( $(value).hasClass('worker-day')  ) { $(value).remove(); }
		});

	};

	var renderWorkerRow = function(worker_id, wRow){

		var momPrevProj = core.config.momCalStart.clone();
		var rlen = wRow.length;
		var dDiff;
		for (var i = 0; i < rlen; i++) { 
			var momCurrentProj = moment( wRow[i].scheduled_date );
			dDiff = momCurrentProj.diff( momPrevProj, 'days');

			if (  dDiff > 0 ) { renderPlaceHolder(worker_id, momPrevProj, dDiff);}
			renderWorkerProj(worker_id, wRow[i]);
			momPrevProj = moment( wRow[i].scheduled_date ).add( wRow[i].job_length_days, 'days' );
		}
		dDiff = core.config.momCalEnd.diff( momPrevProj, 'days' );
		if (  dDiff > 0   )  { renderPlaceHolder(worker_id, momPrevProj, dDiff);}
	};

	var renderPlaceHolder = function(worker_id, start, days){
		var momStart =  ( moment.isMoment(start) ) ? start.clone() : moment( start );
		var proj;
		var selector;
		var cls;
		days = ( ! days ) ? 1 : days;
		if ( days === 1 ){
			cls = "place-holder_"+ worker_id + "_"+ momStart.format(core.config.pwsDateFormat);
			proj = '<div class="cnt-project place-holder '+cls+'"> place holder</div>';
			selector = bldDaySelector(worker_id, momStart);
			$( selector ).append(proj);
		}
		else {
			for( var u=0; u<days;u++){
				var momStartClone = momStart.clone();
				var tDay = momStartClone.add(u, 'days').format(core.config.pwsDateFormat);
				var classDt = 'place-holder_' + worker_id + '_' + tDay;
				proj = '<div class="cnt-project place-holder '+classDt+'"> place holder</div>';
				selector = bldDaySelector(worker_id, tDay);
				$( selector ).append(proj);
			}
		}
	};

	var renderWorkerProj = function(worker_id, scheduleRecord){
		var dys = parseInt(scheduleRecord.job_length_days);
		var momentDrawDay;
		for(var j=0;j<dys;j++){
			// we need to determin what class to apply to the project so the 
			// project appears to span days nicely 
			var cls;
			if (dys === 1) { cls = "proj-single"; }
			else if (j === 0) { cls = "proj-begin"; }
			else if (  j > 0 && j+1 < dys ) { cls = "proj-mid"; }
			else if ( j + 1 === dys ) { cls = "proj-end"; }

			var id = 'schedule-id_'+scheduleRecord.schedule_id+'_dy_'+ Math.abs(j+1);
			//verfiy if we have a sane project id

			var proj = '<div class="proj-draggable cnt-project '+cls+'" id="'+id+'"> <span class="proj-name">'+scheduleRecord.customer_name+'</span><span class="proj-cust-name"></span></div>';
			momentDrawDay = moment(scheduleRecord.scheduled_date).add(j, 'days');

			var selector = bldDaySelector(worker_id, momentDrawDay.format(core.config.pwsDateFormat));

			$( selector ).append(proj);
			$('#'+id).data('scheduleRecord', scheduleRecord );

		}
		//return the last draw day so we can look at the next record and determine
		//if we need to add invisable place holders to keep a nice cal format
		return momentDrawDay;
	};

	var bldDaySelector = function(worker_id, day){
		var mom =  ( moment.isMoment(day) ) ? day.clone() : moment( day );
		return '#worker-id_'+worker_id+'_day_'+mom.format(core.config.pwsDateFormat);
	};

	var  initWorkerDivs = function(targetElement){
		var jqObjTarget = ( targetElement instanceof jQuery ) ? targetElement : $( targetElement );
		var workerDivs = [];
		$.each(core.workers, function( index, value ) {
			buildWorkerDiv(value.id, value.name, jqObjTarget);
		});
	};

	var buildWorkerDiv = function(worker_id, worker_name, targetElement){
		//var jqObjTarget = ( targetElement instanceof jQuery ) ? targetElement : $( targetElement );
		targetElement = $( targetElement );
		var workerDivs = [];

		var workerCntId = 'worker-row_'+ worker_id;
		var divString = '<div class="proj-droppable worker-row" id="'+workerCntId+'"> <div class="worker-name">'+worker_name+'</div> </div> <!-- worker-row -->	';
		targetElement.append(divString);
		
		//build day containers
		buildWorkerDays( worker_id);
	};

	var buildWorkerDays = function(worker_id){
		var daysDivs = [];
		clearWorkerDayDivs( worker_id );
		for(var i = 0; i < core.config.calRangeInt; i++){
			daysDivs.push('<div class="proj-droppable worker-day" id="worker-id_'+worker_id+'_day_'+ core.config.momCalStart.clone().add(i, 'days').format(core.config.pwsDateFormat)+'"> </div>');
		}
		$('#worker-row_'+worker_id).append(daysDivs);
	};


	var setProjUnselected = function(){
		if ( $.isEmptyObject( projectSelected ) ) { return; }
		for (var i = 0; i < projectSelected.job_length_days; i ++) {
			var select =  '#schedule-id_'+projectSelected.schedule_id+'_dy_'+ Math.abs(i+1);
			$( select ).removeClass('proj-selected');
		}
		projectSelected = {};
		$('#selected-project').html('');
	};

	var setProjSelected = function(p){
		for (var i = 0; i < p.job_length_days; i ++) {
			var select =  '#schedule-id_'+p.schedule_id+'_dy_'+ Math.abs(i+1);
			$( select ).addClass('proj-selected');
		}
		projectSelected = p;
		return p;
	};

	var updateSelectedProjectDisplay = function (){
		$('#selected-project').html('');
		var proj = '<li>'+projectSelected.customer_name+'</li>';
		$('#selected-project').append(proj);
		setTagStyles();
	};

	var setTagStyles = function(){
		$.each(projectSelected.tags, function( index, value ) {
			renderTag( value );
		});
	};

	var renderTag = function(tagId){
		var tag = core.options.tags[tagId];
		var styles = 'border-color: '+tag.border_color+'; background-color: '+tag.background_color+'; ';
		$('#selected-project').append('<li class="tag tag-tooltip" id="tag-id_"'+tagId+' style="'+styles+'" tooltip="'+tag.tool_tip+'">'+tag.abbreviation+'</li>');
	};

	// build all the controls that were dynamically built
	var ctrlBind = function(){

		$('#cal-ctrl-back').click(function(e){
			e.preventDefault();
			var nBackward = core.config.navBackward;

			if ( core.config.calNavRootUrl  ) {
				var momTargetDay = core.config.momCalStart.clone();
				momTargetDay.subtract( core.config.navBackward, 'days' );
				calNavRootUrl( momTargetDay );
			}

			navCal( 'backward', nBackward  );
		});

		$('#cal-ctrl-forward').click(function(e){
			e.preventDefault();
			var nForward = core.config.navForward;
			if ( core.config.calNavRootUrl  ) {
				var momTargetDay = core.config.momCalStart.clone();
				momTargetDay.add( core.config.navForward, 'days' );
				calNavRootUrl( momTargetDay );
			}
			navCal( 'forward', nForward  );
		});



		$('.cnt-project').click(function(e){
			// user can click on other elements in the project div
			// make sure we catch it and get the appropriate id

			var projEl;

			if ( e.target.nodeName === "SPAN" ) { projEl = $(e.target).parent(); }
			if ( e.target.nodeName === "DIV" ) { projEl = $(e.target); }

			var projData = projEl.data("scheduleRecord");


			setProjUnselected( projData );
			setProjSelected( projData );
			updateSelectedProjectDisplay();
			
		});

		$("#cnt-worker-grids").scroll(function () { 
			$("#cnt-scroll-header").scrollLeft($("#cnt-worker-grids").scrollLeft());
		});

		// freeze user name containers in place
		$('#cnt-worker-grids').scroll(function(e) {
				var pos = $('#cnt-worker-grids').scrollLeft();
				$(".worker-name, #name-placeholder").css({
						left: pos
				});
		});

		var div= document.getElementById('cnt-worker-grids'); // need real DOM Node, not jQuery wrapper
		var hasVerticalScrollbar= div.scrollHeight>div.clientHeight;
		if ( hasVerticalScrollbar ){
			$('#hdr-row').append('<div class="accomodate-scrollbar">&nbsp</div>');
		}
	};

	return {
		navCal:navCal,
		clearWorkerDayDivs:clearWorkerDayDivs,
		clearWorkerScheduleElements:clearWorkerScheduleElements,
		bldGridHeader:bldGridHeader,
		renderPlaceHolder:renderPlaceHolder,
		renderWorkerProj:renderWorkerProj,
		renderWorkerRow:renderWorkerRow,
		buildWorkerDays:buildWorkerDays,
		buildWorkerDiv:buildWorkerDiv,
		setTagStyles:setTagStyles,
		initWorkerDivs:initWorkerDivs,
		initScheduleRecords:initScheduleRecords,
		setProjSelected:setProjSelected,
		setProjUnselected:setProjUnselected,
		projectSelected:projectSelected,
		updateSelectedProjectDisplay:updateSelectedProjectDisplay,
		ctrlBind:ctrlBind
  	};

};

