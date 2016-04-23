/*global PWSSchedule, moment */
PWSSchedule.core = function(options){
	"use strict";
	
	var scheduleWorkers = options.workerRecords;
	var config = {
		"momCalStart": moment( options.calendarRange.start ),
		"momCalEnd": moment( options.calendarRange.end ),
		"pwsDateFormat": 'YYYY-MM-DD',
		"headerDateFormat": 'ddd MMM D',
		"navBackward": options.settings.navBackward,
		"navForward": options.settings.navForward,
		"navRootUrl": options.settings.navRootUrl,
		"calendarName": options.settings.name
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
