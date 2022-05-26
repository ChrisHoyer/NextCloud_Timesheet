// ==========================================================================================================================================================
// ============================================================== Functional Events =========================================================================
(function() {

	// ===================== Open Settings Button click	
	$("#timesheet-settings-open").click(function() {
		
	var selected_year = $('#timesheet-header-selectionbox-year').val();	
	var selected_month = $('#timesheet-header-selectionbox-month').val();		
	selected_month = (timesheet_MonthNames.indexOf(selected_month) + 1);
	
	// Request Report
	///getReport(selected_year, selected_month, generateSettings);
	
	});	
	

			

// ==========================================================================================================================================================
// ================================================================================= Static Events ==========================================================

		


		
}());