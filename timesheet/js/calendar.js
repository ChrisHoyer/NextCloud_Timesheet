// default app URL
var baseUrl = OC.generateUrl('/apps/timesheet');

// ====================================================================
// ============================== Functional Events ===================
(function() {



// ===================== Add Event into Timesheet
function addEvent2Timesheet(CalTitle, CalArgs) {

	// Request using POST at /record
	var record_url = baseUrl + '/record';
	
	var startdate = new Date(CalArgs.start);
	var enddate = new Date(CalArgs.start);	

	// gather all required data from Arguments
	var record_data = {
		
			// Start/End Time and Date
            startdate: CalArgs.startStr,
			starttime: startdate.toLocaleTimeString('it-IT'),
			
            enddate: CalArgs.endStr,
			endtime: enddate.toLocaleTimeString('it-IT'),
			
			// No Break
			breaktime: "0.0",
			
			// Additional stuff like description and timezone
            description: CalTitle,
            timezoneoffset: new Date().getTimezoneOffset(),
			
			holiday: 			"false",	
			vacation: 			"true",
			unpayedoverhours: 	"false",	
		};
		
	alert(JSON.stringify(record_data));
	
	// POST request with all data
	$.post(record_url, record_data, function() { })
			.done(function(data, status) {
				var response = data;
				alert(response);

			})
			.fail(function() {
				alert( "error" );
			})
			.always(function() {
			});
	}	
	
// ===================== Delete Records for this user
function deleteEventfromTimesheet(recordID) {

	// Request a Delete at /record/{id}
	var record_url = baseUrl + '/record/' + recordID;
		
	// DELETE request with entity ID
	$.ajax({ url: record_url, type: 'DELETE'})
		.done(function () {	
			//alert( "Deleted: " + JSON.stringify(response) );
		 });

}
	
// ============================== Create Calendar ===================	
	document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('timesheet-calendar');

	// Create Calendar
    var calendar = new FullCalendar.Calendar(calendarEl, {
      headerToolbar: {left: 'prev,next today', center: 'title', right: 'dayGridMonth,listYear' },
      initialDate: Date.now(),
	  initialView: 'dayGridMonth',
      selectable: true,
      selectMirror: true,
	  
	  // Create new Calendar Entry
      select: function(arg) {
        var title = "Holiday"
		
		// add event
        if (title) { 
			// Add to Calendar
			calendar.addEvent({ title: title, start: arg.start, end: arg.end, allDay: arg.allDay })
			// Add to Timesheet
			addEvent2Timesheet(title, arg);
        }
		
		// selection cleated
        calendar.unselect()
      },
	  
	  // Click on Event?
      eventClick: function(arg) {
	  
        if (confirm('Are you sure you want to delete ' + arg.event.title + ' ?')) {
			deleteEventfromTimesheet(arg.event.id);
			arg.event.remove();
        }
      },
	  
      editable: true,
      dayMaxEvents: true, // allow "more" link when too many events
	  
	  // Get Events from JSON request
	  events: function(info, successCallback, failureCallback) {

		 // Request using GET at /records
		 var record_url = baseUrl + "/records?start=" + (info.start.valueOf()).toString().slice(0, -3) + "&end=" + (info.end.valueOf()).toString().slice(0, -3);
		  
		 // AJAX call
		 $.ajax({ url: record_url, type: 'GET', headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		  
			// if ajax success
			success: function (response) { 
				var events = [];
				
				// iterate all day events  
				$(response['allEvents']).each(function () { 
					events.push({
						id: $(this).attr('id'),
						title: $(this).attr('description'),
						start: new Date($(this).attr('startdate') + " " + $(this).attr('starttime')),
						end: new Date($(this).attr('enddate') + " " + $(this).attr('endtime')),
						allDay : true, // Events are always all day
						color: 'blue',
					});
				});
				
			successCallback(events);
			}
			
		// Close function
		}); },
	  
    });
	
    calendar.render();
	
  });





}());