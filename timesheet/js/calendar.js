// default app URL
var baseUrl = OC.generateUrl('/apps/timesheet');

// global variable
var calendar;

// ====================================================================
// ============================== Functional Events ===================
(function() {

// ===================== Dialog Form for editing or manually generating records
dialogAddEventForm = $("#dialog-add-event").dialog({
            autoOpen: false,
            height: 400,
            width: 450,
            modal: true,
            buttons: {
				"Add": function(){ addEvent(dialogAddEventForm); },
				Cancel: function() { dialogAddEventForm.dialog( "close" ); }
					},
            close: function() { document.getElementById('form-add-event').reset(); }
		});

	
// ============================== Create Calendar ===================	
	document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('timesheet-calendar');

	// Create Calendar
    calendar = new FullCalendar.Calendar(calendarEl, {
      headerToolbar: {left: 'prev,next today', center: 'title', right: 'dayGridMonth, dayGridWeek, listYear' },
      initialDate: Date.now(),
	  initialView: 'dayGridMonth',
      selectable: true,
      selectMirror: true,
	  weekNumberCalculation: "ISO",
      editable: true,
      dayMaxEvents: false, // allow "more" link when too many events
	  	  
	  // Create new Calendar Entry (open Dialog)
      select: function(arg) { 
	  	
		
			// load content into form
			form = dialogAddEventForm.find( "form" )
			form.find("#timesheet-dialog-startdate").val( extractDate(arg.start) );
			form.find("#timesheet-dialog-enddate").val( extractDate(arg.end-1) );
	  
	  
	  		dialogAddEventForm.dialog("open"); 
	  	},
	  
	  // Click on Event?
      eventClick: function(arg) {
		  
		 // object is editable
		 if(!arg.event.extendedProps.edit) {
			 return;
		 }
	  
        if (confirm('Are you sure you want to delete ' + arg.event.title + ' ?')) {
			deleteRecordID(arg.event.id);
			arg.event.remove();
        }
      },
	  

	  
	  // Get Events from JSON request
	  events: function(info, successCallback, failureCallback) {

		 // Request using GET at /records
		 var record_url = baseUrl + "/records?start=" + (info.start.valueOf()).toString().slice(0, -3) + "&end=" + (info.end.valueOf()).toString().slice(0, -3);
		 record_url = record_url + "&output=list";
		  
		 // AJAX GET Json call
		 $.ajax({
			 headers: {requesttoken: oc_requesttoken},
			 url: record_url,
			 method: 'GET',
			 dataType: 'json',
		  
			// if ajax success
			success: function (response) { 
				var events = [];
				
				// iterate all day events  
				$(response['list']).each(function () {
					
					// check if holiday 
					if($(this).attr('holiday') == "true"){
						events.push({
							id: $(this).attr('id'),
							title: $(this).attr('description'),
							start: new Date($(this).attr('startdate') + " " + $(this).attr('starttime')),
							end: new Date($(this).attr('enddate') + " " + $(this).attr('endtime')),
							allDay : true, // these Events are always all day
							color: 'blue',
							extendedProps: { edit: true }
							});
					} else if ($(this).attr('vacation') == "true"){
						events.push({
							id: $(this).attr('id'),
							title: $(this).attr('description'),
							start: new Date($(this).attr('startdate') + " " + $(this).attr('starttime')),
							end: new Date($(this).attr('enddate') + " " + $(this).attr('endtime')),
							allDay : true, // these Events are always all day
							color: 'red',
							extendedProps: { edit: true }
							});
					} else {
						events.push({
							id: $(this).attr('id'),
							title: $(this).attr('description') + " (" +  $(this).attr('recordduration') + ")",
							start: new Date($(this).attr('startdate') + " " + $(this).attr('starttime')),
							end: new Date($(this).attr('enddate') + " " + $(this).attr('endtime')),
							color: 'green',
							extendedProps: { edit: false }
							});
						
					}
					
				});
								
				
			successCallback(events);
			}
			
		// Close function
		}); },
	  
    });
	
    calendar.render();
	
  });

// ====================================================================
// ============================== Static Events =======================

// ===================== Delete Records for this user
function addEvent(dialogAddEventForm) {

	// gather all data and target
	target = dialogAddEventForm.target;
	form =  dialogAddEventForm.find( "form" );	

	// Request using POST at /record
	var record_url = baseUrl + '/record';

	var record_data = {
			// Start/End Time and Date
            startdate: 			form.find("#timesheet-dialog-startdate").val(),
			enddate: 			form.find("#timesheet-dialog-enddate").val(),	
			
			// full day events	
            starttime: 			"00:00",		
            endtime: 			"23:59",
			breaktime: 			"00:00",
					
			// Selected stuff
            description: 		form.find("#timesheet-dialog-Note").val(),
			holiday: 			form.find("#timesheet-dialog-holiday").prop('checked'),	
			vacation: 			form.find("#timesheet-dialog-vacation").prop('checked'),
			
			unpayedoverhours: 	false,		
            timezoneoffset: 	new Date().getTimezoneOffset()
		};
		
	// POST request with entity ID and new data
	$.ajax({
		headers: {requesttoken: oc_requesttoken},
		url: record_url,
		type: 'POST', 
		data: record_data
		});

	// Info and refresh
	$(dialogAddEventForm).dialog("close");
	
	// generate Event
	var title = form.find("#timesheet-dialog-Note").val();
	calendar.addEvent({ title: title, start: form.find("#timesheet-dialog-startdate").val(), end: form.find("#timesheet-dialog-enddate").val(), allDay: true })
	calendar.unselect()
		
}
}());