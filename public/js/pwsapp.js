var PWSSchedule = PWSSchedule || {};

PWSSchedule.createNS = function (namespace) {
    var nsparts = namespace.split(".");
    var parent = PWSSchedule;
 
    // we want to be able to include or exclude the root namespace so we strip
    // it if it's in the namespace
    if (nsparts[0] === "PWSSchedule") {
        nsparts = nsparts.slice(1);
    }
 
    // loop through the parts and create a nested namespace if necessary
    for (var i = 0; i < nsparts.length; i++) {
        var partname = nsparts[i];
        // check if the current parent already has the namespace declared
        // if it isn't, then create it
        if (typeof parent[partname] === "undefined") {
            parent[partname] = {};
        }
        // get a reference to the deepest element in the hierarchy so far
        parent = parent[partname];
    }
    // the parent is now constructed with empty namespaces and can be used.
    // we return the outermost namespace
    return parent;
};

/*global PWSSchedule, moment */
PWSSchedule.core = function(options){
	"use strict";
//todo: on cal nav project is not highlighted when cal redraws	
	var scheduleWorkers = options.workerRecords;
	var config = {
		"momCalStart": moment( options.calendarRange.start ),
		"momCalEnd": moment( options.calendarRange.end ),
		"pwsDateFormat": 'YYYY-MM-DD',
		"headerDateFormat": 'ddd MMM D',
		"navBackward": options.settings.navBackward,
		"navForward": options.settings.navForward,
		"navRootUrl": options.settings.navRootUrl,
		"calendarName": options.settings.name,
		"calendarId": options.settings.id
	};

	config.calRangeInt = config.momCalEnd.diff( config.momCalStart, "days" );

	var scheduleRecordsByWorkerId = (function(){
		var proj = [];
		$.each(options.scheduleRecords, function(k,v) {
			if ( ! proj.hasOwnProperty(v.worker_id) ) { proj[v.worker_id] = []; }
			proj[v.worker_id].push(v);
		});
		return proj;
	})();

	var workers = (function (){
		var wrk = {};
		$.each(scheduleWorkers, function( index, value ) {
			var obj = new PWSSchedule.worker(value.worker_id, value.worker_name, scheduleRecordsByWorkerId[value.worker_id]);
			wrk[index] =  obj;
		});
		return wrk;
	})();

	return {
		options:options,
		config:config,
		scheduleRecordsByWorkerId:scheduleRecordsByWorkerId,
		workers:workers
	};
 
};

/*global PWSSchedule, moment, crfs,_,Cookies */
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
			var dy = core.config.momCalStart.clone().add(i, 'days');
			var cls = ( isWeekend(dy) ) ? 'cnt-hdr-date weekend' :  'cnt-hdr-date';
			hdrdivs.push('<div class="'+cls+'">'+ core.config.momCalStart.clone().add(i, 'days').format(core.config.headerDateFormat)+' </div>');
		}
		hdrTarget.append(hdrdivs);
		setCalendarName();
	};

	var isWeekend = function(mom){
		
		return (  mom.weekday() === 0 || mom.weekday() === 6 ) ? true : false;

	};

	var initScheduleRecords = function(){
		var rws;
		$.each(core.workers, function( workerIndex, worker ) {
			buildWorkerDays( worker.id );
			rws = worker.buildRows(core.config.momCalStart, core.config.calRangeInt);
			$.each(rws, function( rowIndex, row ) {
				renderWorkerRow( worker.id, row  );
			});
			bindDragDrop(worker.id);
		});
	};

	var renderWorkerScheduleElements = function(workerId, clearExisting){

		if ( clearExisting ) {
			clearWorkerScheduleElements(workerId);
		}

		var worker = core.workers[workerId];
		var rws = worker.buildRows(core.config.momCalStart, core.config.calRangeInt);
		$.each(rws, function( rowIndex, row ) {
			renderWorkerRow( worker.id, row  );
		});

		bindDragDrop(workerId);

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

	};

	var navCal = function(direction, days){
		if ( core.config.navRootUrl ) { 
			calNavRootUrl(direction); 
		}
		if ( direction === 'forward' ) {
			core.config.momCalStart.add( days, 'days' );
			core.config.momCalEnd.add( days, 'days' );
		}

		if ( direction === 'backward' ) {
			core.config.momCalStart.subtract( days, 'days' );
			core.config.momCalEnd.subtract( days, 'days' );
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
		if (  dDiff > 0   )  { 
			renderPlaceHolder(worker_id, momPrevProj, dDiff);
		}
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

	var setCalendarName = function(){

		$('#calendar-name').html(core.config.calendarName);


	};

	var buildWorkerDiv = function(worker_id, worker_name, targetElement){
		//var jqObjTarget = ( targetElement instanceof jQuery ) ? targetElement : $( targetElement );
		targetElement = $( targetElement );
		var workerDivs = [];

		var workerCntId = 'worker-row_'+ worker_id;
		var divString = '<div class="worker-row" id="'+workerCntId+'"> <div class="worker-name">'+worker_name+'</div> </div> <!-- worker-row -->	';
		targetElement.append(divString);
		
		//build day containers
		buildWorkerDays( worker_id);
	};

	var buildWorkerDays = function(worker_id){
		var daysDivs = [];
		clearWorkerDayDivs( worker_id );
		for(var i = 0; i < core.config.calRangeInt; i++){

			var dy = core.config.momCalStart.clone().add(i, 'days');
			var cls = ( isWeekend(dy) ) ? 'weekend' :  '';
			daysDivs.push('<div class="proj-droppable worker-day '+cls+'" id="worker-id_'+worker_id+'_day_'+ core.config.momCalStart.clone().add(i, 'days').format(core.config.pwsDateFormat)+'"> </div>');
		}
		$('#worker-row_'+worker_id).append(daysDivs);
	};

	var updateSelectedProject = function(newRec) {

		if ( ! newRec ) {
			setProjUnselected();	
			return;
		}

		setProjUnselected();
		setProjSelected( newRec );
		updateSelectedProjectDisplay();
		renderButtons();
	};

	var setProjUnselected = function(){
		projectSelected = {};
		$('#selected-project').html('');
		$('#project-controls').html('');
		$('#calendar-workers-scrollable').find('.proj-selected').removeClass('proj-selected');
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
		var proj = '<li id="selected-proj-li" class="proj-draggable">'+projectSelected.customer_name+'</li>';
		$('#selected-project').append(proj);
		$('#selected-proj-li').data("scheduleRecord", projectSelected );
		setTagStyles();
		bindSelectedProjectDraggable();
		//ctrlBind();
	};

	var setTagStyles = function(){
		
		if ( _.isEmpty( projectSelected.tags)  ) {
			return;
		}

		$.each(projectSelected.tags, function( index, value ) {
			renderTag( value );
		});
	};


	var renderExistingTagsForEdit = function(){
		$('#existing-tags').html('');
		if ( _.isEmpty( projectSelected.tags)  ) {
			return;
		}

		$.each(projectSelected.tags, function( index, value ) {
			renderEditTagListElement( value );
		});

		$('.remove-tag').on(' click ', function(e){
			removeTag( $(e.target) );
		});	

	};

	var bindTagCtrls = function(){
		$('#btn-add-tag').on(' click ', function(){
			var tagId = $('#tag-select').val();
			addTag(tagId);
		});
	};

	var removeTag = function(btnTarget){
		var tagId =  btnTarget.attr("id").match(/\d+$/);
		tagId = tagId[0];

		//console.debug('schedule id to remove tag: '+projectSelected.schedule_id);
		//console.debug('tags id '+tagId);
		var jqxhr = $.ajax({
			url: "/calendar/schedule-element/"+projectSelected.schedule_id+"/tag/"+tagId+"/remove",
			async: true,
			method: "GET"
		} )
		.done(function(newRec) {
			if ( _.isObject( newRec )) {
				liveUpdateWorkerRows(projectSelected, newRec);
				renderExistingTagsForEdit();

			}

		})
		.fail(function() {
			alert( "error removing tag!" );
		});
	};

	var addTag = function(tagId){

		if ( $('#tag-select').val() === '' ) {
			alert('select a tag before trying to add');
			return;
		}

		var jqxhr = $.ajax({
			url: "/calendar/schedule-element/"+projectSelected.schedule_id+"/tag/"+tagId+"/add",
			async: true,
			method: "GET"
		} )
		.done(function(newRec) {
			if ( _.isObject( newRec )) {
				liveUpdateWorkerRows(projectSelected, newRec);
				renderExistingTagsForEdit();
			}

		})
		.fail(function() {
			alert( "error adding tag! does it already exist?" );
		});
	};



	var renderEditTagListElement = function(tagId){
		var tag = core.options.tags[tagId];
		var styles = 'border-color: '+tag.border_color+'; background-color: '+tag.background_color+'; ';
		$('#existing-tags').append('<li><span class="tag tag-tooltip" id="tag-id_"'+tagId+' style="'+styles+'" tooltip="'+tag.tool_tip+'">'+tag.name+'</span>&nbsp;<a href="#" class="btn btn-primary btn-xs remove-tag" id="remove-tag_'+tagId+'">remove</a></li>');
	};

	var renderTag = function(tagId){
		var tag = core.options.tags[tagId];
		var styles = 'border-color: '+tag.border_color+'; background-color: '+tag.background_color+'; ';
		$('#selected-project').append('<li class="tag tag-tooltip" id="tag-id_"'+tagId+' style="'+styles+'" tooltip="'+tag.tool_tip+'">'+tag.abbreviation+'</li>');
	};

	var parseWorkerDayId = function(id){

		var stripped = id.substring(10, id.length +1);
		var wkId = stripped.match(/^\d*/g);
		wkId = wkId[0];

		var dt = id.substring(id.length - 10, id.length);

		return { workerId:wkId, day: moment(dt) };
	};

	var liveUpdateWorkerRow = function(newRec) {
		var wkr = core.workers[newRec.worker_id];
		wkr.addProjectToPool(newRec);
		renderWorkerScheduleElements(newRec.worker_id, true);
		updateSelectedProject(newRec);
	};

	var liveUpdateWorkerRows = function(origRec, newRec){

		if( _.isEmpty( origRec ) ) {
			liveUpdateWorkerRow(newRec);
			return;
		}

		var wkr;
		if ( parseInt( origRec.worker_id ) === parseInt( newRec.worker_id )  ) {
			wkr = core.workers[newRec.worker_id];
			wkr.removeProjectFromPool(origRec.schedule_id);
			wkr.addProjectToPool(newRec);
			renderWorkerScheduleElements(newRec.worker_id, true);

		}
		else {
			wkr = core.workers[newRec.worker_id];
			var wkrOld = core.workers[origRec.worker_id];
			wkrOld.removeProjectFromPool(origRec.schedule_id);
			wkr.removeProjectFromPool(origRec.schedule_id);
			wkr.addProjectToPool(newRec);
			renderWorkerScheduleElements(newRec.worker_id, true);
			renderWorkerScheduleElements(origRec.worker_id, true);
		}
		
		updateSelectedProject(newRec);

	};

	var moveScheduleRec = function(scheduleRecord, fieldsObj){
		var jqxhr = $.ajax({
			url: "/RO/schedule/drag-update",
			async: true,
			dataType: "json",
			data: {"_token":crfs,"targetRecord":scheduleRecord, "updateFields": fieldsObj},
			method: "POST"
		} )
		.done(function(newRec) {
			liveUpdateWorkerRows(scheduleRecord, newRec);
		})
		.fail(function() {
			alert( "error" );
		});
	};

	var renderButtons = function(){

		rbUpdate(projectSelected);
		rbRemove(projectSelected);
		rbTags(projectSelected);

		
	};

	var fetchModalAddContent = function(){
		var jqxhr = $.ajax({
			url: '/calendar/'+core.config.calendarId+'/schedule-element/user-add',
			async: true,
			method: "GET"
		} )
		.done(function(response) {
			$('#cal-ctrl').find('.modal-body').html(response)
				.find( "#scheduled-date-field"  )
				.datepicker({dateFormat: "yy-mm-dd"});
			$('#cal-ctrl').find(".modal-title").html("Add Schedule Record");
			$('#btn-modal-cal-ctrl-submit').show();
		})
		.fail(function() {
			alert( "error fetching url data" );
		});

	};

	var buildModalContentFilterWorkers = function(){

		var bdy = $('#cal-ctrl').find('.modal-body');
		$('#cal-ctrl').find('.modal-title').html('Show Workers -- uncheck workers to hide');
		$('#btn-modal-cal-ctrl-submit').hide();
		bdy.html('');
		var filteredWorkers = Cookies.getJSON('pws_filtered_workers');	

		bdy.append('<ul id = "filtered-workers-list"></ul>');
		var list = $('#filtered-workers-list');
		$.each(core.workers, function( index, wkr ) {
			var cid = 'wkr-cb_'+wkr.id;
			list.append('<li class="checkbox" > <input class="fw-checkbox" id="'+cid+'" type="checkbox" name="vehicle" value="'+wkr.id+'" checked="checked">'+wkr.name+' </li>');
			if ( $.inArray(wkr.id, filteredWorkers) >= 0 ) {
				//console.debug(cid);
				$('#'+cid).prop('checked', false);
			}

		});

		$('#filtered-workers-list li .fw-checkbox').on(' click ', function(e){
			var nonCheckedId;
			var clickedId = $(e.target).attr("id").match(/\d+$/);
			var filteredList = [];
			$.each($('.fw-checkbox'), function( index, value ) {
				if ( ! $(value).is(':checked')  ){
					nonCheckedId = $(value).attr("id").match(/\d+$/);
					filteredList.push( parseInt(nonCheckedId[0]));
				}
			});

			Cookies.set('pws_filtered_workers', filteredList, {path:''});
			applyWorkerFilter();

		});
	};

	var applyWorkerFilter = function(){
		var wFilter= Cookies.getJSON('pws_filtered_workers');

		if ( _.isEmpty(wFilter) ) {
			$('#btn-filter-workers').removeClass('btn-warning').addClass('btn-success');
			return;
		}

		$.each( core.workers, function( index, wkr ) {
			if( $.inArray(wkr.id, wFilter) >= 0 ){
				$('#worker-row_'+wkr.id).hide();
			}else{
				$('#worker-row_'+wkr.id).show();
			}
		});

		$('#btn-filter-workers').removeClass('btn-success').addClass('btn-warning');

	};


	var rbTags = function(){


		var html = '<li> <a href="#tags"  id="btn-tags" data-toggle="modal" data-href="" data-name="tags" class="btn btn-info btn-xs" >Tags</a></li>';
		$('#project-controls').append(html);


		$("#tags").on("show.bs.modal", function(e){
			var jqxhr = $.ajax({
				url: "/calendar/"+core.config.calendarId+"/schedule-element/"+projectSelected.schedule_id+"/tag/edit",
				async: true,
				method: "GET"
			} )
			.done(function(response) {

				$('#tags .modal-body').html('');
				$('#tags .modal-body').html(response);
				renderExistingTagsForEdit();
				$('#btn-add-tag').on(' click ', function(){
					var tagId = $('#tag-select').val();
					addTag(tagId);
				});
			
			})
			.fail(function() {
				alert( "error fetching  edit tag data" );
			});
		});
	};

	var rbUpdate = function(rec){
				// Fill modal with content from link href
		$("#myModal").on("show.bs.modal", function(e) {

			//determin what to fetch

			var target = $(e.target);
			var link = $(e.relatedTarget);
			var url = '/calendar/'+core.config.calendarId+'/schedule-element/'+rec.schedule_id+'/user-update';
			var modalBody = $(this).find(".modal-body");

			var jqxhr = $.ajax({
				url: url,
				async: true,
				//dataType: "json",
				//data: {"_token":crfs,"targetRecord":scheduleRecord, "updateFields": fieldsObj},
				method: "GET"
			} )
			.done(function(response) {
				modalBody.html(response).find( "#scheduled-date-field"  ).datepicker({dateFormat: "yy-mm-dd"});

			})
			.fail(function() {
				alert( "error" );
			})
			.always(function() {
				//alert( "complete" );
			});

			$(this).find(".modal-title").html("Update Schedule Element");
		});
		var html = '<li> <a href="#myModal"  data-toggle="modal" data-href="" data-name="Update" class="btn btn-info btn-xs" >Update</a></li>';
		$('#project-controls').append(html);

	};

	var rbRemove = function(){

		var html = '<li> <a href="#" data-name="remove" class="btn btn-info btn-xs btn-remove" >Remove</a></li>';
		$('#project-controls').append(html);

		$(".btn-remove").on("click", function(e) {

		confirm('You are about to remove schedule element for ' + projectSelected.customer_name + '.  Continue?');

		if ( confirm ) {

			var jqxhr = $.ajax({
				url: "/calendar/schedule-element/"+projectSelected.schedule_id+"/remove",
				async: true,
				method: "GET"
			} )
			.done(function(response) {
				
				var wkr = core.workers[projectSelected.worker_id];
				wkr.removeProjectFromPool(projectSelected.schedule_id);
				renderWorkerScheduleElements(projectSelected.worker_id, true);
				setProjUnselected();
				
			
			})
			.fail(function() {
				alert( "error: unable to remove record!" );
			})
			.always(function() {
				//alert( "complete" );
			});

		}

		});



	};

	var bindSelectedProjectDraggable = function(){

		$('#selected-proj-li').draggable( {
			revert : "invalid",
			scroll: true,
			snap: ".proj-droppable",
			snapMode: "inner"
		});

	};

	var bindDragDrop = function(workerId){

		var projElements = $('#worker-row_'+workerId+' .proj-draggable');
		var dayElements = $('#worker-row_'+workerId+' .proj-droppable');

		projElements.draggable( {
			revert : "invalid",
			scroll: true,
			snap: ".proj-droppable",
			snapMode: "inner"
		});

		dayElements.droppable({
			drop: function(event,ui){
				var parsedTargetId = parseWorkerDayId( $(this).attr("id") );
				var rec =  $(ui.draggable).data("scheduleRecord");
				if ( $(ui.draggable).attr("id") === "selected-proj-li"  ) {
					ui.draggable.draggable('option', 'revert', true);
				}

				var targetDay = parsedTargetId.day.format(core.config.pwsDateFormat);
			
				// if target is not goign to change dont hit the database
				if ( targetDay === rec.scheduled_date && parsedTargetId.workerId === rec.worker_id  ) {
					ui.draggable.draggable('option', 'revert', true);
					return true;
				}

				var updateFields = {	
						"scheduled_date":parsedTargetId.day.format(core.config.pwsDateFormat),
						"worker_id":parsedTargetId.workerId
				};

				moveScheduleRec( rec, updateFields );
			},
			accept: ".proj-draggable",
			hoverClass: "drop-hover",
			activeClass: "drop-active"
		});

		projElements.click(function(e){
			// user can click on other elements in the project div
			// make sure we catch it and get the appropriate id

			var projEl;

			if ( e.target.nodeName === "SPAN" ) { projEl = $(e.target).parent(); }
			if ( e.target.nodeName === "DIV" ) { projEl = $(e.target); }

			var projData = projEl.data("scheduleRecord");

			updateSelectedProject( projEl.data("scheduleRecord") );
			
		});

	};


	// build all the controls that were dynamically built
	var ctrlBind = function(){

		$("#cal-ctrl").on("show.bs.modal", function(e) {

			var calCtrl = $('#cal-ctrl');
			
			var dataName = $(e.relatedTarget).attr("data-name");
			
			if ( dataName === "add" ) {
				fetchModalAddContent();
			}

			if ( dataName === "filter-workers" ) {
				buildModalContentFilterWorkers();
			}

		});

		var modal = $('#myModal,#cal-ctrl, #tags');

		modal.css('margin-left', ($(window).width() - modal.width()) / 2 );

		modal.on('submit', function(e){

			e.preventDefault();

			var jqxhr = $.ajax({
				url: $(e.target).attr("action"),
				async: true,
				data: $(e.target).serialize(),
				method: "POST"
			} )
			.done(function(response) {
				if ( _.isObject(response) ) {
					liveUpdateWorkerRows(projectSelected, response);
					$('.modal').modal('hide');
					$('#myModal, #cal-ctrl').find('.modal-body').html('');
				}
				else {
					alert( 'error - repsonse not object' );
				}

			})
			.fail(function(response) {
				if ( _.has(response, 'responseJSON') ){
					$.each(response.responseJSON, function( index, value ) {
						$('.modal-body').append('<h4 class="error">error: '+index+'</h4');
							$.each(value, function( errindex, error ) {
								$('.modal-body').append('<p class="error" >'+error+'</p>');
							});
					});
				}
				else {
					alert('error saving data!');

				}
			});
		});

		$('#btn-modal-submit').on('click', function(){
			$('#schedule-data-form').submit();
		});

		$('#btn-modal-cal-ctrl-submit').on('click', function(){
			$('#schedule-cal-ctrl-form').submit();
		});

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


		// if a scroll bar is on the scrollable div adjus with width so the calendar days line up nicely
		var div= document.getElementById('cnt-worker-grids'); // need real DOM Node, not jQuery wrapper
		var hasVerticalScrollbar= div.scrollHeight>div.clientHeight;
		if ( hasVerticalScrollbar ){
			$('#hdr-row').append('<div class="accomodate-scrollbar">&nbsp</div>');
		}

		//enable drag and drop


	};

	var applyFilters = function(){
		applyWorkerFilter();
	};

	return {
		applyFilters:applyFilters,
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


/*global PWSSchedule, moment, _ */
PWSSchedule.worker =  function(id, name, projectPool) {
	"use strict";
	var rows = [];
	var projects = projectPool;
	//var schRecPool = [];
	//var curentProj;
	//var lastAddedProj;
	

	var getId = function(){
		return id;
	};

	var sortScheduleRecordsAZ = function(recs){
		return recs.sort(function(a, b){
			a = new Date(a.scheduled_date);
			b = new Date(b.scheduled_date);
			if (a < b) { return -1; }
			else if (a > b) { return 1; }
			else { return 0; }
		});
	};

	var sortScheduleRecordsJobLength = function(recs){
		return recs.sort(function(a, b){
			a = a.job_length_days;
			b = b.job_length_days;
			if (a < b) { return -1; }
			else if (a > b) { return 1; }
			else { return 0; }
		});
	};

	var buildRows = function(calStart, calRange){
		//reinit projects;
		rows = [];
		projects = projectPool;
		
		//clear rows and build new ones based on cal range

		calStart = ( moment.isMoment( calStart ) ) ? calStart : moment( calStart );

		if ( ! _.isEmpty( projects ) ) {
			projects = sortScheduleRecordsAZ(projects);
		}

		var row;
		while ( ! _.isEmpty( projects  ) ){
			row = getRowRecords( projects, calStart, calRange );
			if ( _.isEmpty(row) ) {
				break;
			}
			cullProjects(row);
			rows.push( row );
		}
		return rows;
	};


	var cullProjects = function(row){
		var scheduleIds = [];
		$.each(row, function( index, value ) {
			scheduleIds.push( value.schedule_id );
		});

		projects = projects.filter(function(v){
			if (  $.inArray( v.schedule_id, scheduleIds ) !== -1 ) {
				return false;
			}
			else {
				return true;
			}
		});
	};


	var getRowRecords = function(recs, calStart, calRange){
		var lastDate = null;
		var newRow = [];

		for (var i = 0; i < recs.length; i++) { 

			if ( typeof recs[i] === 'undefined' ) {
				throw new Error('holy shit');
			}

			//if record is not within current rendered cal range just skip it
			if ( ! isRecInCalendarRange( recs[i], calStart, calRange )  )  { continue; }
			
			var momNextStart = moment( recs[i].scheduled_date );

			// if its the first row pass just auto add the first record
			if ( lastDate === null ) { //first row pass
				newRow.push( recs[i] );
				lastDate = moment ( recs[i].scheduled_date ).add( recs[i].job_length_days, "days"  );
				continue;
			}

			if ( momNextStart.isAfter( lastDate ) ) {
				newRow.push( recs[i] );
				lastDate = moment ( recs[i].scheduled_date ).add( recs[i].job_length_days, "days"  );
			}
		}
		return newRow;
	};

	var isRecInCalendarRange = function(checkRecord, calStart, calRange){
		var start = moment(  checkRecord.scheduled_date );
		//ensure this returns true if either its start or end date are true
		if ( checkRecord.job_length_days > 1 ) { //check end date as well
			var end = start.clone().add( checkRecord.job_length_days, 'days' );
			if ( isDateInCalendarRange( start, calStart, calRange ) || isDateInCalendarRange( end, calStart, calRange ) ) { return true; }
			return false;
		}
		else {
			if ( isDateInCalendarRange( start, calStart, calRange ) ) { return true; }
			return false;
		}
	};

	var isDateInCalendarRange = function(checkDate, calStart, calRange){
		calStart = ( moment.isMoment( calStart ) ) ? calStart : moment( calStart );
		var	calEnd = calStart.clone().add( calRange, 'days' );
		checkDate =  ( moment.isMoment(checkDate) ) ? checkDate.clone() : moment( checkDate );

		if ( checkDate.isSameOrAfter( calStart  ) && checkDate.isSameOrBefore( calEnd  ) ) { return true; }
		return false;
	};

	var getName = function(){
		return name;
	};

	var removeProjectFromPool = function(scheduleId){
		$.each(projectPool, function( index, value ) {
			if ( scheduleId === value.schedule_id ) {
				projectPool.splice(index,1);
				return false;
			}
		});
	};

	var addProjectToPool = function(rec){
		projectPool.push(rec);
	};


	return {
		id:id,
		removeProjectFromPool:removeProjectFromPool,
		addProjectToPool:addProjectToPool,
		isDateInCalendarRange:isDateInCalendarRange,
		isRecInCalendarRange:isRecInCalendarRange,
		sortScheduleRecordsAZ:sortScheduleRecordsAZ,
		sortScheduleRecordsJobLength:sortScheduleRecordsJobLength,
		getRowRecords:getRowRecords,
		name:name,
		getId:getId,
		getName:getName,
		buildRows:buildRows,
		rows:rows,
		projects:projects
	};




};


//# sourceMappingURL=pwsapp.js.map
