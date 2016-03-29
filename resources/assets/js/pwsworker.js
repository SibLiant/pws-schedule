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
		//console.debug(id);
		//console.debug(rows);
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

		//console.log('recs length '+recs.length);
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


	return {
		id:id,
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
