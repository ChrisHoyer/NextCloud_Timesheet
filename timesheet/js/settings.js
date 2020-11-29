// default app URL
var baseUrl = OC.generateUrl('/apps/timesheet');

// ==========================================================================================
// ============================== Functional Events ===================
(function() {
	
	
	// ===================== Save Button click
	$("#timesheet-settings-save").click(function() {
	
	// Request using POST at /record
	var record_url = baseUrl + '/report';

	// Get Selected Data
	var selected_year = $('#timesheet-header-selectionbox-year').val();	
	var selected_month = $('#timesheet-header-selectionbox-month').val();	
			
	var settings_data = {
			// Working Hours and Days
            regularweeklyhours: $("#timesheet-settings-regularweeklyhours").val(),		
            workingdDayMon: 	$("#timesheet-settings-DayMon").prop('checked'),
			workingdDayTue: 	$("#timesheet-settings-DayTue").prop('checked'),
			workingdDayWed: 	$("#timesheet-settings-DayWed").prop('checked'),
			workingdDayThu: 	$("#timesheet-settings-DayThu").prop('checked'),
			workingdDayFri: 	$("#timesheet-settings-DayFri").prop('checked'),
			workingdDaySat: 	$("#timesheet-settings-DaySat").prop('checked'),
			workingdDaySun: 	$("#timesheet-settings-DaySun").prop('checked'),
			
			// selected sheet
			monyearid: 			selected_year + "," + selected_month
		};
		
	// POST request with all data
	$.post(record_url, settings_data, function() { })
			.done(function(response) {
				alert( response );
	
			})
			.fail(function() {
				alert( "error" );
			})
			.always(function() {
			})
	});
	
	}());