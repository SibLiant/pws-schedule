var PschGrid = (function ($,moment, undefined) {
		var initvars = [];
		var rootDate;
		var range;
		var rootEndDate;
		var remaining;
		var projSelected;
		var gridId;

		var init = function(root_dt){
			if ( root_dt ){
				rootDate = moment(root_dt, 'YYYY-MM-DD').format('YYYY-MM-DD');
			}
			else{
				rootDate = moment().format('YYYY-MM-DD');
			}
			console.debug(rootDate);
			//range = initvars.settings.range;
			range = 30;
			rootEndDate = moment(rootDate).add(range, 'days').format('YYYY-MM-DD');
			$.ajax({
				url: '/ajax_init',
				type: 'POST',
				data: {range_start:rootDate,range_end:rootEndDate},
				success: function (data, textStatus, jqXHR) {
					//console.debug(data);
					initvars.projects = data.projects;
					initvars.users = data.installers;
					//initvars.settings = data.settings;
					//initvars.days = data.settings.days;
					bldGridHeader();
					initUsersAssigned();
					initProjects();
				}
			});
		}

		var reInit = functio(root_dt){
			$('#calendar-users-scrollable').html('');
			$( "div.hdr-row"  ).html('');
			init(root_dt);
		}

		var initUsersAssigned = function(){
			$.each(initvars.users, function(k,v) {
				bldGridUser(v);
			});
		}

		var setProjSelected = function(p){
			p = $( p );
			pdata = p.data('project_data');
			projSelected = p.data('project_data');
			for (i = 0; i < projSelected.days; i ++) {
				select =  '#project-user-join-id_'+projSelected.project_id+'_dy_'+ Math.abs(i+1);
				$( select ).addClass('proj-selected');
			}
			updateSelectedProjectDisplay();
			//console.debug(projSelected);
		}

		var setProjUnselected = function(){
			if ( $.isEmptyObject( projSelected ) ) return;
			for (i = 0; i < projSelected.days; i ++) {
				select =  '#project-user-join-id_'+projSelected.project_id+'_dy_'+ Math.abs(i+1);
				$( select ).removeClass('proj-selected');
			}
			projSelected = {};
			$('#selected-project').html('');
		}

		var refreshProjectsByUserId = function (user_id, rangeStart, rangeEnd){
			dt = {"user_id":user_id, "rangeStart":rangeStart, "rangeEnd":rangeEnd};
			$.ajax({
				url: '/ajax_get_projects_by_user',
				type: 'POST',
				dataType: 'json',
				data: dt,
				complete: function (jqXHR, textStatus) {
					// callback
				},
				success: function (data, textStatus, jqXHR) {
					clearUserGrid(user_id);
					addProjectsToAUserGrid({"id":user_id}, data);
					bindUI();
				},
				error: function (jqXHR, textStatus, errorThrown) {
					// error callback
				}
			});
		}

		var clearUserGrid = function(user_id){
			grid = $('#user-row_'+user_id).find('.user-day');
			$.each(grid, function(k,v) {
				$(v).html('');
			});
		}

		var initProjects = function(){
			//add all projects for a given user - 1 user at a time
			$.each(initvars.users, function(k,v) {
				//console.debug(v.id);
				user_projects = filterUserProjects(v.id, initvars.projects);
				//console.debug(user_projects);

				//verify there are projects to add and add them
				if (user_projects.length !== 0) {
					addProjectsToAUserGrid(v, user_projects); 
				}
			});
			bindUI();
		}


		var bldGridHeader = function(){
			hdrdivs = [];
			//build the controls first
			hdrdivs.push('<div id="name-placeholder"> <a href="#" id="cal-ctrl-back"><</a> <a href="#" id="cal-ctrl-forward">></a> </div>');
									
			for(i=0;i<range;i++){
				hdrdivs.push('<div class="cnt-hdr-date">'+moment(rootDate).add(i, 'days').format('ddd MMM D')+' </div>')
			}
			$( "div.hdr-row"  ).append(hdrdivs);

			//bind the controls we just added
			$('#cal-ctrl-back').click(function(e){
				e.preventDefault();
				new_root_dt = moment(rootDate,'YYYY-MM-DD').subtract(1, 'month');
				PschGrid.reInit(new_root_dt.format('YYYY-MM-DD'));
			});

			$('#cal-ctrl-forward').click(function(e){
				new_root_dt = moment(rootDate,'YYYY-MM-DD').add(1, 'month');
				PschGrid.reInit(new_root_dt.format('YYYY-MM-DD'));
			});
		}


		var  bldGridUser = function(user){
			div = [];
			div.push('<div class="proj-droppable user-row" id="user-row_'+user.id+'"> <div class="user-name">'+user.fname+' '+user.lname+'</div> </div> <!-- user-row -->	');
			$( 'div#calendar-users-scrollable' ).append(div);
			
			//build day containers
			days = [];
			for(i = 0; i < range; i++){
				days.push('<div class="proj-droppable user-day" id="user-id_'+user.id+'_day_'+moment(rootDate).add(i, 'days').format('YYYY-MM-DD')+'"> </div>');
			}
			$( "div#user-row_"+user.id  ).append(days);
		}

		var addProjectsToAUserGrid = function(user, projects){
			//console.debug(user);
			//console.debug(projects);

			res = filterSingleRow(projects);
			
			remaining = res.remaining;
			bldRow( user.id, res.row );

			while ( remaining.length > 0 ) {
				res = filterSingleRow(remaining);
				remaining = res.remaining;
				bldRow( user.id, res.row );
			}
		}


		var bldRow = function(user_id, prow){
			//console.debug(user_id);
			//console.debug(prow);
			for(i=0; i<prow.length; i++){
				//check for first row - add fillers from beginning to first project
				if (i==0) { 
					mom_lp_ed = moment(rootDate);
					mom_next_sd = moment(prow[i].scheduled_date);
					insertPlaceholderSpan(user_id, mom_lp_ed, mom_next_sd);
				}
				
				//last element in row fill until end of grid
				if ( i == prow.length -1  ) {
					mom_lp_ed = moment(prow[i].scheduled_date).add(prow[i].days, 'days');
					mom_next_sd = moment(rootEndDate);
					insertPlaceholderSpan(user_id, mom_lp_ed, mom_next_sd);
				}

				// from the last project until this next project
				if (  i != 0  ) {
					mom_lp_ed = moment(prow[i-1].scheduled_date).add(prow[i-1].days, 'days');
					mom_next_sd = moment(prow[i].scheduled_date);
					insertPlaceholderSpan(user_id, mom_lp_ed, mom_next_sd);
				}
				addSingleProject(user_id, prow[i]);
			}
		}

		var filterSingleRow = function(proj){
			//console.debug(proj);
			lp = proj.slice(0);
			srow = [];
			//last_added_end_date = '';
			last_added_project = {};
			for ( i = 0; i<proj.length; i++) {
				if ( i == 0 ) { // this is the first record and will be automatically added
					srow.push(proj[i]);
					last_added_project = proj[i];

					//last_added_end_date = moment(proj[i].scheduled_date).add(proj[i].days -1, 'days').format('YYYY-MM-DD');
					delete lp[i];
					continue;
				}

				if ( ! isOverlap( last_added_project, proj[i] ) ) {
					srow.push(proj[i]);
					last_added_project = proj[i];
					delete lp[i];
				}
			}

			//reindex lp arra
			rlp = [];
			$.each(lp, function(k,v) { 
				if ( typeof v != 'undefined' ) rlp.push(v); 
			});
			return {"remaining":rlp, "row":srow};
		}

		var isOverlap = function(a, b) {
			//determin if project a's finish date overlaps with project b's beging date
			a_finish_date = moment(a.scheduled_date).add(a.days, 'days').unix();
			b_begin_date = moment(b.scheduled_date).unix();
			if ( a_finish_date < b_begin_date ) return false;
			return true;
		}

		var addSingleProject = function(user_id, project){
			//console.debug(project);
			dys = parseInt(project.days);
			for(j=0;j<dys;j++){
				// we need to determin what class to apply to the project so the project appears to span days nicely if it spans multiple days
				
				if (dys == 1) cls = "proj-single";
				else if (j == 0) cls = "proj-begin";
				else if (  j > 0 && j+1 < dys ) cls = "proj-mid";
				else if ( j + 1 == dys ) cls = "proj-end";

				id = 'project-user-join-id_'+project.project_id+'_dy_'+ Math.abs(j+1);
				//verfiy if we have a sane project id

				proj = '<div class="proj-draggable cnt-project '+cls+'" id="'+id+'"> <span class="proj-name">'+project.customer_lname+'</span><span class="proj-cust-name"></span></div>';
				selector = bldDaySelector(user_id, moment(project.scheduled_date).add(j, 'days').format('YYYY-MM-DD'));
				$( selector ).append(proj);

				//bind the project data into the jquery selector
				$( '#'+id ).data('project_data', project);
				dt = $( '#'+id ).data('project_data');
			}
		} 

		var insertPlaceholderSpan = function(user_id, mom_start, mom_end){
			df = mom_end.diff(mom_start, 'days');
			for(u=0; u<df;u++){
				instart =  moment(mom_start.format('YYYY-MM-DD'));
				class_dt = instart.add(u, 'days').format('YYYY-MM-DD');
				addPlaceHolder(user_id, class_dt);
			}
		}

		var addPlaceHolder = function(user_id, day){
			proj = '<div class="cnt-project place-holder id="place-holder_'+user_id+'_'+day+'"> place holder</div>';
			selector = bldDaySelector(user_id, day);
			$( selector ).append(proj);
		}

		var bldDaySelector = function(user_id, day){
				return '#user-id_'+user_id+'_day_'+day;
		}

		var filterUserProjects = function(user_id, projects){
			projs = [];
			$.each(projects, function(k,v) {
				if (user_id == v.installer_user_id) projs.push(v); 
			});
			return projs;
		}


		var bindUI = function (){
			$( ".proj-draggable" ).draggable({
				scope: 'projects',
				revert: true,
				revertDuration: 200,
				snap: ".proj-droppable",
				snapTolerance: 20,
				distance: 10
			});

			$( ".proj-droppable" ).droppable({
				drop: handleDropEvent,
				//accept: "proj-draggble",
				activeClass: "drop-active-class",
				hoverClass: "proj-drop-hover",
				scope: 'projects',
				greedy: true,
			});


			$('.cnt-project').click(function(e){
				//unslect proj if one is selected
				setProjUnselected();
				setProjSelected( e.currentTarget );
			});

			$('.proj-selectable').selectable({
				selected: function( event, ui ) { alert( 'selected');}	
			});
		}

		var updateSelectedProjectDisplay = function (){

			//$.getJSON( '/ajax_get_selected_project/'+projSelected.project_id,  function( data ) {
				//console.debug(data);
			//});
			$('#cnt-ctrl').load('/ajax_get_selected_project/'+projSelected.project_id, function(){
				//bindUI();

			$( ".proj-draggable" ).draggable({
				scope: 'projects',
				revert: true,
				revertDuration: 200,
				snap: ".proj-droppable",
				snapTolerance: 20,
				distance: 10
			});
				//console.debug('proj - selected');
				//console.debug(projSelected);
				$( '#proj-ctrl').data('project_data', projSelected);
			});

		}

		var buildSelectedProjControls = function(){

		}
var handleDropEvent = function( event, ui ) {
			draggable_id = ui.draggable.attr("id");
			droppable_id = $(this).attr("id");

			dropped_proj_data = $('#'+ui.draggable.attr("id")).data('project_data');
			//console.debug(dropped_proj_data);


			
			project = dropped_proj_data;
			id_parsed = parseDayCellId(droppable_id);
			moveProject( project, id_parsed.user_id, id_parsed.day );
		}

		var parseDayCellId = function(id){
			user_id = id.match(/(?:user-id_)(\d+)/);
			day = id.match(/(.{10}$)/);
			return {"user_id":user_id[1], "day":day[0]};
		}


		var moveProject = function( project, target_user, target_day  ){
			dt = {
				"project":project,
				"target_user":target_user,
				"target_day":target_day,
			};

			$.ajax({
				url: '/ajax_move_scheduled_project',
				type: 'POST',
				dataType: 'json',
				data: dt,
				complete: function (jqXHR, textStatus) {
					// callback
				},
				success: function (data, textStatus, jqXHR) {
					//console.debug(project);
					if (project.user_id != target_user) { //update both user grids
						refreshProjectsByUserId(project.installer_user_id, rootDate, rootEndDate);
						refreshProjectsByUserId(target_user, rootDate, rootEndDate);
					}
					else {
						refreshProjectsByUserId(project.installer_user_id, rootDate, rootEndDate);
					}
				},
				error: function (jqXHR, textStatus, errorThrown) {
					alert( "error on ajax moveProject - " + errorThrown );
				}
			});

		}

		return {
			//vars
			"rootDate":rootDate,
			"rootEndDate":rootEndDate,
			"clearUserGrid":clearUserGrid,

			//functions
			"init":init,
			"reInit":reInit,
			"initvars":initvars,
			"bindUI":bindUI
		};

})(jQuery, moment);




/*
 *		PschUuser closure
 *
 */

var PschUser = function(jQuery, undefined){	
	
	var current_schedule_id;

	function saveManageUsers(manageUserData){
		//console.debug(manageUserData);
		$.ajax({
			url: 'pschusers/manage',
			type: 'POST',
			dataType: 'json',
			data: {"user_data":manageUserData},
			complete: function (jqXHR, textStatus) {
				// callback
			},
			success: function (data, textStatus, jqXHR) {
				$.ajax({
					url: '/pschusers/manage',
					type: 'POST',
					//dataType: 'xml/html/script/json',
					data: manageUserData,
					complete: function (jqXHR, textStatus) {
						// callback
					},
					success: function (data, textStatus, jqXHR) {
						location.reload();
					},
					error: function (jqXHR, textStatus, errorThrown) {
						// error callback
					}
				});
			},
			error: function (jqXHR, textStatus, errorThrown) {
				// error callback
			}
		});
	}
		
	function refreshAssignedUsers(schedule_id){
		current_schedule_id = schedule_id;
		url = '/pschedule/'+current_schedule_id+'/manageAssignedUsers';
		//console.debug(url);
		$.ajax({
			url: url,
			type: 'GET',
			success: function (data, textStatus, jqXHR) {
				//console.debug(data);
				$( "#assigned-users-results").empty().html(data);

				//bind remove button
				$('.btn-remove-schedule-user').click(function(e){
					e.preventDefault();
					url = $(this).attr("href");	
					$.ajax({
						url: url,
						type: 'GET',
						success: function (data, textStatus, jqXHR) {
							refreshAssignedUsers(current_schedule_id);
						},
					});

				});

				//bind add user dropdown
				$('#select-add-user').change(function(e){
					user_id = $('#select-add-user').val();
					if ( user_id !== 0) {
						$.ajax({
							url: '/pschedule/'+current_schedule_id+'/modifyAssignedUser/'+user_id+'/attach',
							type: 'GET',
							success: function (data, textStatus, jqXHR) {
							refreshAssignedUsers(current_schedule_id);
							}
						});
					}
				});

			}
		});
	}

		return {
			saveManageUsers:saveManageUsers,
			refreshAssignedUsers:refreshAssignedUsers
		}
}(jQuery);

$(document).ready(function() {

	$('.confirm-delete').click(function(e){
		sure = confirm('About to delete a record.  Are you sure?');
		if (sure) return true;
		return false;
	});


	//define the users dialog popup
	$( "#pop-cnt-users"  ).dialog({ 
		autoOpen: false,
		width: "800",
		modal: true,
		title: "Manage Users",
		position: { my: "top", at: "center top+50px" }
	});	

	//define the users dialog popup
	$( "#pop-assigned-users"  ).dialog({ 
		autoOpen: false,
		width: "800",
		modal: true,
		title: "Manage Assigned Users",
		position: { my: "top", at: "center top+50px" }
	});	

	//define the projects dialog popup
	$( "#pop-cnt-projects"  ).dialog({ 
		autoOpen: false,
		width: "800",
		modal: true,
		title: "Manage Projects",
		position: { my: "top", at: "center top+50px" }
	});	

	$('#btn-users').click( function(e){
		e.preventDefault();
		$( "#cnt-user-results"  ).empty();
		$( "#pop-cnt-users"  ).dialog( "open"  );
	});

	$('#btn-projects').click( function(e){
		e.preventDefault();
		$( "#cnt-project-results"  ).empty()
		$( "#pop-cnt-projects"  ).dialog( "open"  );
	});

	$('#btn-import-users').click(function(e){
		e.preventDefault();
		//alert('win');
		
		$.ajax({
			url: '/pschusers/import',
			type: 'GET',
			//dataType: 'xml/html/script/json',
			//data: $.param( $('Element or Expression') ),
			success: function (data, textStatus, jqXHR) {
				$( "#cnt-user-results").empty().html(data);
				// success callback
			},
			error: function (jqXHR, textStatus, errorThrown) {
				//alert('error fetching users for import!');
			}
		});
	});


	$('#btn-manage-projects').click(function(e){
		e.preventDefault();
		$.ajax({
			url: '/pschprojects/index',
			type: 'GET',
			success: function (data, textStatus, jqXHR) {
				$( "#cnt-project-results").empty().html(data);

				$('#btn-manage-projects-save').click(function(e){
					e.preventDefault();
					// go through the check boxes and build data for posting
					//manageUserData = [];
					//$('.user-row').each(function(k,v){
						//id = $(v).attr("id");
						//pschUserId = id.substr(id.lastIndexOf('_') + 1);
						//console.debug(v)
						//admin= $(v).find('.check-admin');
						//admin = ( $(admin).prop('checked') ) ? "1" : "0";
						//assign = $(v).find('.check-assign');
						//assign = ( $(assign).prop('checked') ) ? "1" : "0";
						//manageUserData.push({"id":pschUserId, "admin":admin, "assign_projects":assign});
					//});
					//post the state of the checkboxes
					PschUser.saveManageUsers(manageUserData);
				})
			},
			error: function (jqXHR, textStatus, errorThrown) {
				//alert('error fetching users for import!');
			}
		});
	});

	$('#btn-manage-users').click(function(e){
		e.preventDefault();
		$.ajax({
			url: '/pschusers/manage',
			type: 'GET',
			success: function (data, textStatus, jqXHR) {
				$( "#cnt-user-results").empty().html(data);

				$('#btn-manage-users-save').click(function(e){
					e.preventDefault();
					// go through the check boxes and build data for posting
					manageUserData = [];
					$('.user-row').each(function(k,v){
						id = $(v).attr("id");
						pschUserId = id.substr(id.lastIndexOf('_') + 1);
						//console.debug(v)
						admin= $(v).find('.check-admin');
						admin = ( $(admin).prop('checked') ) ? "1" : "0";
						assign = $(v).find('.check-assign');
						assign = ( $(assign).prop('checked') ) ? "1" : "0";
						manageUserData.push({"id":pschUserId, "admin":admin, "assign_projects":assign});
					});
					//post the state of the checkboxes
					PschUser.saveManageUsers(manageUserData);
				})
			},
			error: function (jqXHR, textStatus, errorThrown) {
				//alert('error fetching users for import!');
			}
		});
	});

	$('.btn-assigned-users').click(function(e){
		e.preventDefault();
		id = $(e.target).attr('id');
		schedule_id = id = id.substr(id.lastIndexOf('_') + 1);
		$( "#pop-assigned-users-results"  ).empty();
		$( "#pop-assigned-users"  ).dialog( "open"  );
		PschUser.refreshAssignedUsers(schedule_id);

	});
});


$(document).ready(function() {
	PschGrid.init();
	PschGrid.bindUI();
	// synce the calendar horizontal scroll
	$("#cnt-user-grids").scroll(function () { 
        $("#cnt-scroll-header").scrollLeft($("#cnt-user-grids").scrollLeft());
	});

	// freeze user name containers in place
	$('#cnt-user-grids').scroll(function(e) {
			pos = $('#cnt-user-grids').scrollLeft();
			$(".user-name, #name-placeholder").css({
					left: pos
			});
	});






});

