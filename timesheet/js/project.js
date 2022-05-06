// ==========================================================================================================================================================================
// ================================================================================= Functional Events ======================================================================
(function() {

	// ===================== After Pageload
	document.addEventListener('DOMContentLoaded', function() {
		
		// Request Reports from this user
		getProjectList(generateProjectList);
		
	});
	
	// ===================== Submit Button click
	$("#timesheet-newproject-submit").click(function() {
		
		// gather all required data from form
		var request_data = {
			
				// Record ID
				id: "",
				
				// Project and Description
				projectname: $('#timesheet-newproject-name').val(),
				description: $('#timesheet-newproject-description').val(),
							
				// Additional stuff which is not implemented in frontend	
			};
			
		
		// POST request with all data
		createupdateProject(request_data, RefreshProject());
		
	});
	
// ======================================================================================================================================================================
// ================================================================================= Static Events ======================================================================

// ===================== Load Records for this user in time periode
function RefreshProject() {
		
	// Request Projects
	getProjectList(generateProjectList);
	
}
// ========================================================================================================================================================================
// ================================================================================= Page Generating ======================================================================
function generateProjectList(projectlist)
{
	// table content
	var content = [];
	
		
	// ===================== Generate Record Table ===================	
	
	// Iterate all record items in List
	$.each(projectlist.report, function (day_index, day_entity){
		
		// header information about overtime
		if(day_entity.difference_duration_hours !=0 ){
			var overtime = day_entity.difference_duration;	
			var overtime_sign = (day_entity.difference_duration_hours > 0) ? "pos": (day_entity.difference_duration_hours < 0) ? "neg": "none";						
		} else { var overtime = ""; var overtime_sign="none"; }

		// create table for current day entry day header
		var row_day = [];
		row_day = "<div class='timesheet-report-day timesheet-report-row-d" +  day_entity.day + " timesheet-report-row-e" +  day_entity.eventtype + "' >" + "<div class='timesheet-report-day-header' >";
		row_day	= row_day + "<div class='timesheet-report-day-header-date'>" + day_entity.day + ", " + day_entity.date  + "</div>";
		row_day	= row_day + "<div class='timesheet-report-day-header-workinghours'>" + day_entity.total_duration + "</div>";
		row_day	= row_day + "<div class='timesheet-report-day-header-overtime timesheet-report-overtimesgn-" + overtime_sign +"'>" + overtime + "</div>";
		row_day	= row_day + "<div class='timesheet-report-day-header-events'>" + day_entity.eventtype + "</div></div>";
		 	
		// day_entity contains records
		if(day_entity.hasOwnProperty('records')) {
			
			// Iterate all records
			$.each(day_entity.records, function (record_index, record_entity){
									
				// Generate table row content
				var record_table_row = "<div class='timesheet-report-day-content' >";
			
				// Generate first column of object
				record_table_row = record_table_row + "<div class='timesheet-report-cell timesheet-report-column-date'></div>";		
				record_table_row = record_table_row + "<div class='timesheet-report-cell timesheet-report-column-start'>" + record_entity.starttime + "</div>";
				record_table_row = record_table_row + "<div class='timesheet-report-cell timesheet-report-column-end'>" + record_entity.endtime + "</div>";
				record_table_row = record_table_row + "<div class='timesheet-report-cell timesheet-report-column-break'>" + record_entity.breaktime + "</div>";
				record_table_row = record_table_row + "<div class='timesheet-report-cell timesheet-report-column-duration'>" + record_entity.recordduration + "</div>";		
									
				// Generate clickable trash can (trash can icon implemented by nextcloud env, https://docs.nextcloud.com/server/15/developer_manual/design/icons.html)
				// Generate clickable edit (edit icon implemented by nextcloud env, https://docs.nextcloud.com/server/15/developer_manual/design/icons.html)
				record_table_row = record_table_row + "<div class='timesheet-report-day-content-cell timesheet-report-column-modify'>";
				
				if(recordlist.settings.signedoff == "0")
				{
					record_table_row = record_table_row + "<span class='timesheet-record-delete icon-delete' data-dbid=" + record_entity.id + "></span>";
					record_table_row = record_table_row + "<span class='timesheet-record-edit icon-rename' data-startdate='" + record_entity.startdate + "'";
					record_table_row = record_table_row + " data-starttime='" + record_entity.starttime + "' data-endtime='" + record_entity.endtime + "' data-description='" + record_entity.description + "'";
					record_table_row = record_table_row + " data-holiday='" + record_entity.holiday + "' data-vacation='" + record_entity.vacation + "' data-unpayedoverhours='" + record_entity.unpayedoverhours + "'";
					record_table_row = record_table_row + " data-breaktime='" + record_entity.breaktime + "' data-dbid=" + record_entity.id + " ></span>";
				}
				
				record_table_row = record_table_row + "</div>";
					
				// Description
				record_table_row = record_table_row + "<div class='timesheet-report-day-content-cell timesheet-report-column-description'>" + record_entity.description + "</div>";
						
				// End Table Row
				record_table_row = record_table_row + "</div>";
				row_day = row_day + record_table_row;
					
			});	
		}
			
		row_day	= row_day + "</div>";
				
		// Include into Table
		content.push(row_day.toString());
		
		// include into barchart data
		barchart_date.unshift(day_entity.date);
		barchart_recordduration.unshift(day_entity.total_duration_hours);
		barchart_targetduration.unshift(day_entity.target_workduration_hours);		
		if(day_entity.difference_duration_hours <= 0){ barchart_differenceduration.unshift(0); } else { barchart_differenceduration.unshift(day_entity.difference_duration_hours); }
					
	});
	
	
	// ===================== Display HTML Table and update BarChar ===================
	$("#timesheet-report-content").html($( "<div/>", { "class": "timesheet-report-content-generated", html: content.join( "" )}));
					
	updateBarChart(barchart_date, barchart_recordduration, barchart_targetduration, barchart_differenceduration);

	// ===================== Generate Report summary Row ===================
			
	// header information about overtime
	if(recordlist.summary.difference_duration_hours !=0 ){
			var monthly_difference = recordlist.summary.difference_duration;	
			var monthly_difference_sign = (recordlist.summary.difference_duration_hours > 0) ? "pos": (recordlist.summary.difference_duration_hours < 0) ? "neg": "none";						
		} else { var monthly_difference = ""; var monthly_difference_sign="none"; }
	
	if(recordlist.summary.total_overtime_hours !=0 ){
			var total_difference = recordlist.summary.difference_duration_total;	
			var total_difference_sign = (recordlist.summary.total_overtime_hours > 0) ? "pos": (recordlist.summary.total_overtime_hours < 0) ? "neg": "none";						
		} else { var total_difference = ""; var total_difference_sign="none"; }
	
	// report row
	var report_row = [];	
	

	// Generate table row content
	report_row = "<div class='timesheet-record-summary-row'>";
	
	// Generate table report row content
	report_row = report_row + "<div class='timesheet-record-table-summary-leftfiller'></div>";	
	report_row = report_row + "<div class='timesheet-record-summary-workinghours'> <span>&Sigma;</span> " + recordlist.summary.total_duration + "</div>";
	report_row = report_row + "<div class='timesheet-record-summary-monthlyovertime timesheet-report-overtimesgn-" + monthly_difference_sign +"'>" + monthly_difference + "</div>";
	report_row = report_row + "<div class='timesheet-record-summary-totalovertime timesheet-report-overtimesgn-" + total_difference_sign +"'> Total: " + total_difference + "</div>";
	
	// End Table Row
	report_row = (report_row + "</div>").toString();
		
	
	// Include into Table
	$("#timesheet-report-summary").html($( "<div/>", {
                      "class": "timesheet-report-summary-generated",
                      html: report_row
                    }));			

					
	// ===================== Delete Click ===================
	$(".timesheet-record-delete").click(function(e) {
		
		// Ask for permission
	 	var result = confirm( "Delete ID: " + $(e.target).data("dbid") + " ?");
		
		if (result == true) { deleteRecordID($(e.target).data("dbid")); }
	
	});

	// ===================== Edit Click ===================
	$(".timesheet-record-edit").click(function(e) {
		
		// Default action will not occour
		e.preventDefault();
		dialogModifyRecordForm.target = e.target;
		
		// load content into form
		form = dialogModifyRecordForm.find( "form" )
		form.find("#timesheet-dialog-startdate").val($(e.target).data("startdate"));
		form.find("#timesheet-dialog-starttime").val($(e.target).data("starttime"));
		form.find("#timesheet-dialog-endtime").val($(e.target).data("endtime"));
		form.find("#timesheet-dialog-breaktime").val($(e.target).data("breaktime"));
		form.find("#timesheet-dialog-description").val($(e.target).data("description"));
		form.find("#timesheet-dialog-unpayedoverhours").attr( 'checked', $(e.target).data("unpayedoverhours") );
		
		// Open dialog form
		dialogModifyRecordForm.dialog("open");
		
	
	});	
	
	// refresh settings according to current timesheet
	refreshSettings(recordlist.settings);	
		
		
	
};
	
	
}());