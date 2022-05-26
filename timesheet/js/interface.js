// default app URL
var baseUrl = OC.generateUrl('/apps/timesheet');

// ====================================================================
// ============================== Functional Events for API ===========

// ===================== Load Reports of this user
function getReportList() {
	
	// Request using POST at /record
	var record_url = baseUrl + '/reports';
		
	// GET request with all data from userID
	$.getJSON(record_url, function() {})
			.done(function(data, status) {
				
			return data;
										
			})
			.fail(function(response) {
				alert( "error:" + response );
			})
			.always(function() {
			});	
}


// ===================== Load Records for this user for given Month/year
function getRecordList_Month(selected_year, selected_month) {

	// Request using POST at /record
	var record_url = baseUrl + "/records?year=" + selected_year + "&month=" + selected_month;
	
	// GET request with all data from userID
	$.getJSON(record_url, function() {})
			.done(function(data, status) {
				
				// Generate Table
				generateRecordList(data);
					
			})
			.fail(function() {
				alert( "error" );
			})
			.always(function() {
			});	
}


// ===================== Delete Records for this user
function editRecordForm(dialogModifyRecordForm) {

	// gather all data and target
	target = dialogModifyRecordForm.target;
	form =  dialogModifyRecordForm.find( "form" );
	
	// Request a PUT at /record/{id}
	var record_url = baseUrl + '/record/' + $(target).data('dbid');

	var record_data = {
			// Start/End Time and Date
            startdate: 			form.find("#timesheet-dialog-startdate").val(),		
            starttime: 			form.find("#timesheet-dialog-starttime").val(),		
            endtime: 			form.find("#timesheet-dialog-endtime").val(),
			enddate: 			form.find("#timesheet-dialog-startdate").val(),		
			// Breaktime
			breaktime: 			form.find("#timesheet-dialog-breaktime").val(),		
			// Additional stuff like description and timezone
            description: 		form.find("#timesheet-dialog-description").val(),
			holiday: 			form.find("#timesheet-dialog-holiday").prop('checked'),	
			vacation: 			form.find("#timesheet-dialog-vacation").prop('checked'),
			unpayedoverhours: 	form.find("#timesheet-dialog-unpayedoverhours").prop('checked'),		
            timezoneoffset: new Date().getTimezoneOffset()
		};
			
	// PUT request with entity ID and new data
	$.ajax({ url: record_url, type: 'PUT', data: record_data,})
		.done(function (response) {	});

	// Info and refresh
	$(dialogModifyRecordForm).dialog("close");
	getRecordList();

}

// ===================== Delete Records for this user
function deleteRecord(recordID) {

	// Request a Delete at /record/{id}
	var record_url = baseUrl + '/record/' + recordID;
		
	// DELETE request with entity ID
	$.ajax({ url: record_url, type: 'DELETE'})
		.done(function () {	
			//alert( "Deleted: " + JSON.stringify(response) );
		 });

	// Info and refresh
	getRecordList();

}

