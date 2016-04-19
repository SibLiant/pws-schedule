/*global PWSSchedule, moment, affix, spyOnEvent*/
/*exported  getCalStart, getCalRange, setCalStart */
/* jshint unused:false */
describe('PWSCore - ', function() {
	"use strict";
	var core;
	var testJson;

	beforeAll(function() {

		testJson = 
				{
					"auth": {
						"username": "parker",
						"key": "asdfifeilsdfkjlkjsdf"
					},
					"calendarRange": {
						"start": "2016-03-14",
						"end": "2016-04-13"
					},
					"scheduleRecords": {

						"199": {
							"worker_name": "46q9wXh6d6Rypt9",
							"customer_name": "0tO0jxxNxspUjl2",
							"project_id": 40,
							"customer_id": 14,
							"worker_id": 4,
							"schedule_id": 199,
							"scheduled_date": "2016-03-24",
							"job_length_days": 3,
							"schedule_note": null,
							"external_link": null,
							"tags": [4, 3, 2, 1]
						},
						"200": {
							"worker_name": "eftC9Pban1nS6ty",
							"customer_name": "uulKcPwdFZNnj8P",
							"project_id": 48,
							"customer_id": 4,
							"worker_id": 2,
							"schedule_id": 200,
							"scheduled_date": "2016-03-31",
							"job_length_days": 1,
							"schedule_note": null,
							"external_link": null,
							"tags": [4, 3, 2, 1]
						},
						"201": {
							"worker_name": "Peters",
							"customer_name": "Mr. Parker Bradtmiller",
							"project_id": 201,
							"customer_id": 51,
							"worker_id": 1,
							"schedule_id": 201,
							"scheduled_date": "2016-03-14",
							"job_length_days": 1,
							"schedule_note": null,
							"external_link": null
						},

						"202": {
							"worker_name": "Peters",
							"customer_name": "Mark Crabil",
							"project_id": 204,
							"customer_id": 55,
							"worker_id": 1,
							"schedule_id": 209,
							"scheduled_date": "2016-03-15",
							"job_length_days": 2,
							"schedule_note": null,
							"external_link": null
						},
						"204": {
							"worker_name": "Peters",
							"customer_name": "Orvil Red",
							"project_id": 208,
							"customer_id": 58,
							"worker_id": 1,
							"schedule_id": 211,
							"scheduled_date": "2016-03-23",
							"job_length_days": 3,
							"schedule_note": null,
							"external_link": null
						},

						"205": {
							"worker_name": "46q9wXh6d6Rypt9",
							"customer_name": "0tO0jxxNxspUjl2",
							"project_id": 40,
							"customer_id": 14,
							"worker_id": 1,
							"schedule_id": 213,
							"scheduled_date": "2016-03-13",
							"job_length_days": 1,
							"schedule_note": null,
							"external_link": null,
							"tags": [4, 3, 2, 1]
						},
						"206": {
							"worker_name": "eftC9Pban1nS6ty",
							"customer_name": "uulKcPwdFZNnj8P",
							"project_id": 48,
							"customer_id": 4,
							"worker_id": 1,
							"schedule_id": 214,
							"scheduled_date": "2016-03-13",
							"job_length_days": 3,
							"schedule_note": null,
							"external_link": null,
							"tags": [4, 3, 2, 1]
						},
						"207": {
							"worker_name": "Peters",
							"customer_name": "Mr. Parker Bradtmiller",
							"project_id": 201,
							"customer_id": 51,
							"worker_id": 1,
							"schedule_id": 215,
							"scheduled_date": "2016-04-10",
							"job_length_days": 1,
							"schedule_note": null,
							"external_link": null
						},

						"208": {
							"worker_name": "Peters",
							"customer_name": "Mark Crabil",
							"project_id": 204,
							"customer_id": 55,
							"worker_id": 1,
							"schedule_id": 216,
							"scheduled_date": "2016-04-10",
							"job_length_days": 5,
							"schedule_note": null,
							"external_link": null
						},
						"209": {
							"worker_name": "Peters",
							"customer_name": "Orvil Red",
							"project_id": 208,
							"customer_id": 58,
							"worker_id": 1,
							"schedule_id": 217,
							"scheduled_date": "2016-03-23",
							"job_length_days": 3,
							"schedule_note": null,
							"external_link": null
						}






					},
					"settings": {
						"navForward": "30",
						"navBackward": "30"
					},
					"workerRecords": [{
						"worker_id": 1,
						"worker_name": "Peters"
					}, {
						"worker_id": 2,
						"worker_name": "eftC9Pban1nS6ty"
					}, {
						"worker_id": 3,
						"worker_name": "YlRtlX8WuX8VloT"
					}, {
						"worker_id": 4,
						"worker_name": "46q9wXh6d6Rypt9"
					}, {
						"worker_id": 5,
						"worker_name": "Q5qkaeLcBGgMrqj"
					}],
					"tags": {
						"1": {
							"id": 1,
							"name": "guitar box",
							"abbreviation": "GB",
							"tool_tip": "testing tool tip",
							"background_color": "blue",
							"border_color": "grey"
						},
						"2": {
							"id": 2,
							"name": "drump box",
							"abbreviation": "PB",
							"tool_tip": "testing tool tip",
							"background_color": "orange",
							"border_color": "black"
						},
						"3": {
							"id": 3,
							"name": "piano box",
							"abbreviation": "XXL",
							"tool_tip": "testing tool tip",
							"background_color": "silver",
							"border_color": "yellow"
						},
						"4": {
							"id": 4,
							"name": "speaker box",
							"abbreviation": "HVHC",
							"tool_tip": "testing tool tip",
							"background_color": "green",
							"border_color": "red"
						}
					}
				};





		core =  new PWSSchedule.core(testJson);
		core.render =  new PWSSchedule.render( core );
		
	});



	afterAll(function() {

		var json = null;

	});



	it('test the create name space function', function() {
		PWSSchedule.createNS( 'PWSSchedule.parker_is.cool' );

		expect(PWSSchedule.parker_is).toEqual( jasmine.any( Object ) );
		expect(PWSSchedule.parker_is.cool).toEqual( jasmine.any( Object ) );
		PWSSchedule.createNS( '' );
		expect(PWSSchedule).toEqual( jasmine.any( Object ) );

	});

	it('check that a new core was instantiated', function() {
		expect(core).toEqual(jasmine.any(Object));
	});


	it('verifies shedule recs are sorted into var scheduleRecordsByWorkerId', function() {
		expect(core.scheduleRecordsByWorkerId[1][0].worker_name).toEqual('Peters');
	});

	it('verifies workers object and a worker', function() {
		expect(core.workers[0]).toEqual( jasmine.objectContaining({ name: "Peters" }));

	});



	describe('test worker obj #### ', function() {

		var wkr;

		beforeAll(function() {
			wkr = new PWSSchedule.worker( 1, 'parker', core.scheduleRecordsByWorkerId[1]    );
		});

		it('verify instantiation', function() {

			expect(wkr).toEqual( jasmine.any( Object ) );
			expect(wkr.name).toEqual( 'parker' );
			expect(wkr.id).toEqual(1);
		});

		it('gets its id', function() {
			expect(wkr.getId()).toEqual(1);
		});


		it('gets its name', function() {
			expect(wkr.getName()).toEqual('parker');
		});


		it('sortes records by date cronological', function() {
			var sorted = wkr.sortScheduleRecordsAZ( wkr.projects );
			var s2 = $.extend(true, {}, sorted);

			var lastKey = Object.keys(s2).pop();
			var firstKey = Object.keys(s2).reverse().pop();

			expect( s2[lastKey] ).toEqual( jasmine.objectContaining({scheduled_date:"2016-04-10"}) );
			expect( s2[firstKey] ).toEqual( jasmine.objectContaining( {scheduled_date: "2016-03-13"} ) );
		});


		it('sortes records by job_length_days cronologically', function() {

			var sorted = wkr.sortScheduleRecordsJobLength( wkr.projects );
			var s2 = $.extend(true, {}, sorted);

			var lastKey = Object.keys(s2).pop();
			var firstKey = Object.keys(s2).reverse().pop();

			expect( s2[lastKey] ).toEqual( jasmine.objectContaining({job_length_days:5}) );
			expect( s2[firstKey] ).toEqual( jasmine.objectContaining( {job_length_days: 1} ) );
		});


		//need to test some dependancies first before we can start to test the build rows 
		it('tests if date is in calendar range', function() {
			var inRange;
			var momDt = core.config.momCalStart.clone();
			var range = core.config.calRangeInt;


			//test first day that cal should start on based on core config 
			inRange = wkr.isDateInCalendarRange( momDt, momDt.format(core.config.pwsDateFormat), range);
			expect(inRange).toEqual(true);

			//test the day right before the calendar config 
			inRange = wkr.isDateInCalendarRange( momDt.clone().subtract(1, 'day'), momDt.format(core.config.pwsDateFormat), range);
			expect(inRange).toEqual(false);


			//test the last day of the calendar range
			inRange = wkr.isDateInCalendarRange( momDt.clone().add(range, 'day'), momDt.format(core.config.pwsDateFormat), range);
			expect(inRange).toEqual(true);


			//test the last day of range plus one
			inRange = wkr.isDateInCalendarRange( momDt.clone().add(range + 1, 'day'), momDt.format(core.config.pwsDateFormat), range);
			expect(inRange).toEqual(false);

		});

		it('test if record is in calendar range', function() {

			var inRange;
			var momDt = core.config.momCalStart.clone();
			var range = core.config.calRangeInt;
			var dtForm = core.config.pwsDateFormat;

			var rec = testJson.scheduleRecords[209];

			inRange = wkr.isRecInCalendarRange(rec, momDt.format(dtForm), range);
			expect(inRange).toEqual(true);

			rec.scheduled_date = momDt.clone().add( range, 'days' ).format( dtForm );
			inRange = wkr.isRecInCalendarRange(rec, core.config.momCalStart.format(dtForm), range);
			expect(inRange).toEqual(true);

			//test day after range
			rec.scheduled_date = moment( rec.scheduled_date ).add( 1, 'days' ).format( core.PWSDateFormat );
			inRange = wkr.isRecInCalendarRange(rec, core.config.momCalStart, range);
			expect(inRange).toEqual(false);

		});


		it('takes projects array does a single pass and returns new array of non overlaping records', function() {
			
			var recs = wkr.getRowRecords( wkr.projects, core.config.momCalStart, core.config.calRangeInt );

			//this should just fetch 2 records for now  - we'll verify by schedule id which should be unique
			expect(recs[0]).toEqual( jasmine.objectContaining( {schedule_id: 201} ) );
			expect(recs[1]).toEqual( jasmine.objectContaining( {schedule_id: 215} ) );
			expect(recs[2]).toBeUndefined();
		});

		it('processes all projects on a worker into rows', function() {
			//start with a re-inited wkr obj
			wkr = new PWSSchedule.worker( 1, 'parker', core.scheduleRecordsByWorkerId[1]    );
			var recs = wkr.buildRows(core.config.momCalStart, core.config.calRangeInt);

			//should get back 3 rows verify each row and the array position
			//if this changes then the rows are being built differnetly  and we need
			//to ensure we understand why
			expect(recs[0][0].schedule_id).toEqual(214);
			expect(recs[0][1].schedule_id).toEqual(211);
			expect(recs[0][2].schedule_id).toEqual(215);

			expect(recs[1][0].schedule_id).toEqual(201);
			expect(recs[1][1].schedule_id).toEqual(216);

			expect(recs[2][0].schedule_id).toEqual(209);
		});

	});

	describe('tests render obj --- ', function() {

		var fixcnt;
		var container;
		beforeEach(function(){

		});

		//test header
		it('test render header elements', function() {


			$('body').append('<div class="hdr-row" style="border: 1px solid black;"></div>');
			core.render.bldGridHeader('.hdr-row');
			expect( $('#cal-ctrl-back') ).toBeInDOM();
			expect( $('#cal-ctrl-forward') ).toBeInDOM();
			expect( $('.cnt-hdr-date') ).toBeInDOM();
			$('.hdr-row').remove();
		});


		//add worker day divs to a target
		it('test building of worker day divs -- buildWorkerDays() ', function() {

			$('body').append('<div id="worker-row_1" style="border: 1px solid black;"></div>');
			core.render.buildWorkerDays(1, '#worker-row_1');
			expect( $('#worker-row_1') ).toContainElement( $('#worker-id_1_day_2016-03-14') );
			$('#worker-row_1').remove();

		});

		//build a whole worker div
		it('test build worker div -- buildWorkerDiv()', function() {
			$('body').append('<div id="calendar-workers-scrollable" style="border: 1px solid black;"></div>');
			core.render.buildWorkerDiv( 1, 'parker', '#calendar-workers-scrollable' );
			expect( $('#worker-row_1')).toContainElement( $('.worker-name') );
			core.render.buildWorkerDays( 1, '#worker-row_1' );
			expect( $('#worker-id_1_day_2016-03-15') ).toBeInDOM();
		});



		describe('projects and place holder elements ## ', function() {

			it('renders place holders ', function() {
				core.render.renderPlaceHolder(1, '2016-03-14', 1);
				expect( $('#worker-id_1_day_2016-03-14') ).toBeInDOM();

				expect( $('.place-holder_1_2016-03-14') ).toBeInDOM();
				
				//test 3 day span place holder
				core.render.renderPlaceHolder(1, '2016-03-15', 3);
				expect( $('.place-holder_1_2016-03-15') ).toBeInDOM();
				expect( $('.place-holder_1_2016-03-16') ).toBeInDOM();
				expect( $('.place-holder_1_2016-03-17') ).toBeInDOM();
			});


			it('render single day worker project', function() {
				var wkr = new PWSSchedule.worker( 1, 'parker', core.scheduleRecordsByWorkerId[1] );
				core.render.renderWorkerProj(1, wkr.projects[2]);
				expect( $('#schedule-id_201_dy_1') ).toBeInDOM();
			});

			it('render multi day worker project', function() {
				var wkr = new PWSSchedule.worker( 1, 'parker', core.scheduleRecordsByWorkerId[1] );
				core.render.renderWorkerProj(1, wkr.projects[3]);
				expect( $('#schedule-id_209_dy_1') ).toBeInDOM();
				expect( $('#schedule-id_209_dy_2') ).toBeInDOM();
			});



			it('clears all elements from worker day containers but leaves the day containers ', function() {
				core.render.clearWorkerScheduleElements( 1 );
				expect( $('#schedule-id_209_dy_1') ).not.toBeInDOM();
				expect( $('#schedule-id_209_dy_2') ).not.toBeInDOM();
				expect( $('#schedule-id_201_dy_1') ).not.toBeInDOM();
			});

			it('renders a full worker row form wkr object', function() {
				var wkr = new PWSSchedule.worker( 1, 'parker', core.scheduleRecordsByWorkerId[1] );
				var rws = wkr.buildRows( core.config.momCalStart, core.config.calRangeInt );
				core.render.renderWorkerRow(1, rws[0]);
				expect( $('#schedule-id_211_dy_1') ).toBeInDOM();
				expect( $('#schedule-id_211_dy_2') ).toBeInDOM();
				expect( $('#schedule-id_211_dy_3') ).toBeInDOM();
				
				expect( $('#schedule-id_215_dy_1') ).toBeInDOM();
				//clear rows to render for next test
				core.render.clearWorkerScheduleElements(1);

			});

			it('renders a full second row from wkr object', function() {
				var wkr = new PWSSchedule.worker( 1, 'parker', core.scheduleRecordsByWorkerId[1] );
				var rws = wkr.buildRows( core.config.momCalStart, core.config.calRangeInt );
				core.render.renderWorkerRow(1, rws[1]);
				expect( $('#schedule-id_216_dy_1') ).toBeInDOM();
				expect( $('#schedule-id_216_dy_2') ).toBeInDOM();
				expect( $('#schedule-id_216_dy_3') ).toBeInDOM();
				
			});


			it('clears a worker div of all day containers', function() {
				core.render.clearWorkerDayDivs( 1 );
				expect( $('#schedule-id_209_dy_1') ).not.toBeInDOM();
				expect( $('#schedule-id_201_dy_1') ).not.toBeInDOM();
			});


			it('init all schedule records initScheduleRecords()', function() {
				core.render.initWorkerDivs('#calendar-workers-scrollable');
				core.render.initScheduleRecords();
                var txt = $('#worker-row_5 > div.worker-name').html();
                //last record that was rended in testing env
				expect(txt).toContain('Q5qkaeLcBGgMrqj');
			});


			it('selects a project', function() {
				var target = $('#schedule-id_199_dy_1');
				var dt = target.data('scheduleRecord');
				core.render.setProjSelected( dt );
				expect( target ).toHaveClass('proj-selected');
			});

			it('un-selects a project', function() {
				var target = $('#schedule-id_199_dy_1');
				core.render.setProjUnselected();
				expect( target ).not.toHaveClass('proj-selected');
			});

			it('adjusts the project display in the header appropriately upon selection', function() {
				$('body').append('<ul id="selected-project" style="border: 1px solid black;"></ul>');

				var target = $('#schedule-id_199_dy_1');
				var data = core.render.setProjSelected( target );
				core.render.updateSelectedProjectDisplay();
				expect( $( '#selected-project'  ) ).toContainHtml('<li>'+data.customer_name+'</li>');

				
			});


			it('binds the calendr interface', function() {
				$('body').append('<ul id="cnt-worker-grids" style="border: 1px solid black;"></ul>');
				core.render.ctrlBind();

				$('body').append('<div class="hdr-row" style="border: 1px solid black;"></div>');
				core.render.bldGridHeader('.hdr-row');
				
			});

			it('verifies project clicked for selection', function() {
				var spyProjClick = spyOnEvent('.cnt-project', 'click');
				$('.cnt-project').first().click();
				expect('click').toHaveBeenTriggeredOn('.cnt-project');
				expect(spyProjClick).toHaveBeenTriggered();
				
			});

			it('verifies calendar back nav element is functional', function() {
				//spy on cal movement events
				core.render.ctrlBind();

				var spyEventBack = spyOnEvent('#cal-ctrl-back', 'click');
				$('#cal-ctrl-back').click();
				expect('click').toHaveBeenTriggeredOn('#cal-ctrl-back');
				expect(spyEventBack).toHaveBeenTriggered();

			});

			it('verifies calendar forward nav element is functionsl', function() {

				$('.hdr-row').remove();
				$('body').append('<div class="hdr-row" style="border: 1px solid black;"></div>');
				core.render.bldGridHeader('.hdr-row');
				core.render.ctrlBind();


				var spyEventF = spyOnEvent('#cal-ctrl-forward', 'click');
				$('#cal-ctrl-forward').click();
				expect('click').toHaveBeenTriggeredOn('#cal-ctrl-forward');
				expect(spyEventF).toHaveBeenTriggered();
				
			});
			
		});


  });

});
