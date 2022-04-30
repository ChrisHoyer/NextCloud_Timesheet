// ==========================================================================================================================================================================
// ================================================================================= Functional Events ======================================================================
(function() {

	// ===================== After Pageload
	document.addEventListener('DOMContentLoaded', function() {
		
		// Request Reports from this user
		getReportList(generateReport);
		
	});

	// ===================== Dialog Form for editing or manually generating records
	dialogModifyRecordForm = $("#dialog-modify-record").dialog({
				autoOpen: false,
				height: 400,
				width: 450,
				modal: true,
				buttons: {
					"Edit Record": function(){ editRecord(dialogModifyRecordForm); },
					Cancel: function() { dialogModifyRecordForm.dialog( "close" ); }
						},
				close: function() { $("#dialog-modify-record")[0].reset();}
	});

		  
	// ===================== Submit Button click
	$("#timesheet-newrecord-submit").click(function() {
		
		// gather all required data from form
		var request_data = {
			
				// Record ID
				id: "",
				
				// Start/End Time and Date
				startdate: $('#timesheet-newrecord-date').val(),
				enddate: $('#timesheet-newrecord-date').val(),
				starttime: $('#timesheet-newrecord-starttime').val(),
				endtime: $('#timesheet-newrecord-endtime').val(),
				breaktime: $('#timesheet-newrecord-breaktime').val(),
				
				// Additional stuff like description and timezone
				description: $('#timesheet-newrecord-description').val(),
				timezoneoffset: 	new Date().getTimezoneOffset(),
				holiday: 			"false",	
				vacation: 			"false",
				unpayedoverhours: 	"false",	
			};
			
		
		// POST request with all data
		createupdateRecord(request_data, RefreshTimesheet());
		
	});

	// ===================== Refresh Button click
	$("#timesheet-newrecord-refresh").click(function() { RefreshTimesheet(); });
	
	// ===================== Save Button click
	$("#timesheet-settings-save").click(function() {
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
			
				// report time frame
				startreport: $("#timesheet-settings-startreport").val(),	
				endreport: $("#timesheet-settings-endreport").val(),	
				timezoneoffset: new Date().getTimezoneOffset(),
			
				// selected sheet
				monyearid: 			selected_year + "," + (timesheet_MonthNames.indexOf(selected_month)+1)
			};
			
		// Send Data
		createupdateReport(settings_data);
		RefreshTimesheet();
			
	});

	// ===================== Report Sign-off Button Click
	$("#timesheet-report-signed").click(function() {
		// Get Selected Data
		var selected_year = $('#timesheet-header-selectionbox-year').val();	
		var selected_month = $('#timesheet-header-selectionbox-month').val();
		
		var reportid = {

				// selected sheet
				monyearid: 			selected_year + "," + (timesheet_MonthNames.indexOf(selected_month)+1)
			
			};
			
		// Send Data
		signReport(reportid);
		RefreshTimesheet();
		
		});
	
// ======================================================================================================================================================================
// ================================================================================= Static Events ======================================================================

// ===================== Load Records for this user in time periode
function RefreshTimesheet() {
	
	// Get Selected Data
	var selected_year = $('#timesheet-header-selectionbox-year').val();	
	var selected_month = $('#timesheet-header-selectionbox-month').val();
	selected_month = (timesheet_MonthNames.indexOf(selected_month) + 1);
	
	// Request Records
	getRecords(selected_year, selected_month,"" ,"" , "report", generateRecordList);
	
}

// ===================== Refresh Settings-Tab
function refreshSettings(settings) {
			
	
		// Some Information
		document.getElementById("timesheet-settings-regularweeklyhours").value = parseFloat(settings.regularweeklyhours) ;
		document.getElementById("timesheet-settings-label").innerHTML = "Settings for " + settings.monyearid.split(",")[0] + " - " + settings.monyearid.split(",")[1];	

		// report timeframe
		if(parseFloat(settings.startreport)){ document.getElementById("timesheet-settings-startreport").value = settings.startreport; }
		else {document.getElementById("timesheet-settings-startreport").value = settings.reportdatemin; }
		document.getElementById("timesheet-settings-startreport").setAttribute("max", settings.reportdatemax);
		document.getElementById("timesheet-settings-startreport").setAttribute("min", settings.reportdatemin);	

		if(parseFloat(settings.endreport)){ document.getElementById("timesheet-settings-endreport").value = settings.endreport; }
		else {document.getElementById("timesheet-settings-endreport").value = settings.reportdatemax; }	
		document.getElementById("timesheet-settings-endreport").setAttribute("max", settings.reportdatemax);
		document.getElementById("timesheet-settings-endreport").setAttribute("min", settings.reportdatemin);	
	
		// check working days
		regulardays = settings.regulardays.split(",");
		if(regulardays.includes("Mon")){ document.getElementById("timesheet-settings-DayMon").checked = true} 
		else { document.getElementById("timesheet-settings-DayMon").checked = false} 
		if(regulardays.includes("Tue")){ document.getElementById("timesheet-settings-DayTue").checked = true}
		else { document.getElementById("timesheet-settings-DayTue").checked = false} 
		if(regulardays.includes("Wed")){ document.getElementById("timesheet-settings-DayWed").checked = true}
		else { document.getElementById("timesheet-settings-DayWed").checked = false} 
		if(regulardays.includes("Thu")){ document.getElementById("timesheet-settings-DayThu").checked = true}
		else { document.getElementById("timesheet-settings-DayThu").checked = false} 
		if(regulardays.includes("Fri")){ document.getElementById("timesheet-settings-DayFri").checked = true}
		else { document.getElementById("timesheet-settings-DayFri").checked = false} 
		if(regulardays.includes("Sat")){ document.getElementById("timesheet-settings-DaySat").checked = true}
		else { document.getElementById("timesheet-settings-DaySat").checked = false} 	
		if(regulardays.includes("Sun")){ document.getElementById("timesheet-settings-DaySun").checked = true}
		else { document.getElementById("timesheet-settings-DaySun").checked = false} 	
	
	}

// ===================== Delete Records for this user
function editRecord(dialogModifyRecordForm) {

	// gather all data and target
	target = dialogModifyRecordForm.target;
	form =  dialogModifyRecordForm.find( "form" );

	var request_data = {
			
			// Record id
			id: 				$(target).data('dbid'),
			
			// Start/End Time and Date
            startdate: 			form.find("#timesheet-dialog-startdate").val(),		
            starttime: 			form.find("#timesheet-dialog-starttime").val(),		
            endtime: 			form.find("#timesheet-dialog-endtime").val(),
			enddate: 			form.find("#timesheet-dialog-startdate").val(),		
			breaktime: 			form.find("#timesheet-dialog-breaktime").val(),
			
			// Additional stuff like description and timezone
            description: 		form.find("#timesheet-dialog-description").val(),
			holiday: 			"false",
			vacation: 			"false",
			unpayedoverhours: 	form.find("#timesheet-dialog-unpayedoverhours").prop('checked'),		
            timezoneoffset: new Date().getTimezoneOffset()
		};
			
		// POST request with all data
		createupdateRecord(request_data, RefreshTimesheet());

	// Info and refresh
	$(dialogModifyRecordForm).dialog("close");
	RefreshTimesheet();

}

// ========================================================================================================================================================================
// ================================================================================= Page Generating ======================================================================

// ===================== Generates Table from Recordlist JSON Response
function generateRecordList(recordlist){
	
	// table content
	var content = [];
	
	// content for barchart
	var barchart_date = [];
	var barchart_recordduration = [];
	var barchart_targetduration = [];
	var barchart_differenceduration = [];
		
	// ===================== Check if signed or not ===================	
	
	// Disable if signed
	document.getElementById("timesheet-newrecord-date").disabled = recordlist.settings.signedoff == "0" ? false : true;
	document.getElementById("timesheet-newrecord-starttime").disabled = recordlist.settings.signedoff == "0" ? false : true;
	document.getElementById("timesheet-newrecord-endtime").disabled = recordlist.settings.signedoff == "0" ? false : true;
	document.getElementById("timesheet-newrecord-breaktime").disabled = recordlist.settings.signedoff == "0" ? false : true;
	document.getElementById("timesheet-newrecord-description").disabled = recordlist.settings.signedoff == "0" ? false : true;
	document.getElementById("timesheet-newrecord-submit").disabled = recordlist.settings.signedoff == "0" ? false : true;
		
	// ===================== Generate Record Table ===================	
	
	// Iterate all record items in List
	$.each(recordlist.report, function (day_index, day_entity){
		
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

// ===================== Generates Table from Reportlist JSON Response
function generateReport(reportlist){
	
		// Generate HTML code for Report Header
		var TScontent_selection = [];
		var current_date = new Date();
		
		// Generate table row content
		TScontent_selection = "<div class='timesheet-header-selectionbox-content' >";

		// Generate selectionbox
		TScontent_selection = TScontent_selection + "<select id='timesheet-header-selectionbox-year' name='year' class='timesheet-header-selectionbox' >";
		
		// Iterate all years
		$.each(reportlist.reports, function (record_year, report_month){
			if(record_year == current_date.getFullYear()){
				TScontent_selection = TScontent_selection + "<option selected='selected'>" + record_year + "</option>";		
			} else
				TScontent_selection = TScontent_selection + "<option>" + record_year + "</option>";
		});
				
		// End Table Row
		TScontent_selection = TScontent_selection + "</select><select id='timesheet-header-selectionbox-month' name='month'  class='timesheet-header-selectionbox' >";
			
		$.each(reportlist.reports[current_date.getFullYear()], function (record_index, report_month){	
	
			if(report_month == (current_date.getMonth()+1))
				TScontent_selection = TScontent_selection + "<option selected='selected'>" + timesheet_MonthNames[report_month-1] + "</option>";			
			else
				TScontent_selection = TScontent_selection + "<option>" + timesheet_MonthNames[report_month-1] + "</option>";
		});
		
		TScontent_selection = TScontent_selection + "</select></div>";
	
		// Button for sign-off
		TScontent_selection = TScontent_selection +

				
		// Include into Table
		$("#timesheet-header-selectionbox").html($( "<div/>", {
                      "class": "timesheet-record-table-report-generated",
                      html: TScontent_selection.toString()
                    }));
						
	// ===================== Year changed ===================					
	$('#timesheet-header-selectionbox-year').change(function () {
		
		// Get selected year and corresponding months
		var year = $(this).val();
		var cormonths = reportlist.reports[year];

		var html_options = $.map(cormonths, function(month){
								return '<option value="' + timesheet_MonthNames[month-1] + '">' + timesheet_MonthNames[month-1] + '</option>'
						}).join('');	
							
		$("#timesheet-header-selectionbox-month").html(html_options);
		RefreshTimesheet();		
			
    });


	// ===================== Month changed ===================					
	$('#timesheet-header-selectionbox-month').change(function () {
		RefreshTimesheet();
		});

	// Everything is setup? Refresh page
	RefreshTimesheet();			
};

// ===================== Generate BarChart
function updateBarChart(barchart_date, barchart_recordduration, barchart_targetduration, barchart_differenceduration){
	
	// reset canvas element
	 $('#timesheet-record-recordgraph-chart').remove(); // this is my <canvas> element
	 $('#timesheet-record-recordgraph').append('<canvas id="timesheet-record-recordgraph-chart"><canvas>');
	
	// get Cavas Element
	var c = document.getElementById("timesheet-record-recordgraph-chart");
	var ctx = c.getContext("2d");
	
	// draw chart
	var myChart = new Chart(ctx, {
		type: 'bar',
    data: {
        labels: barchart_date,
        datasets: [{
			label: 'worked time',
            backgroundColor: 'rgb(255, 99, 132)',
            borderColor: 'rgb(255, 99, 132)',
			stack: 'Stack 1',
            data: barchart_recordduration
        },{
			label: 'target workingtime',
            backgroundColor: 'rgb(99, 132, 255)',
            borderColor: 'rgb(99, 104, 255)',
			stack: 'Stack 0',
            data: barchart_targetduration
        },{
			label: 'overtime',
            backgroundColor: 'rgb(132, 255, 99)',
            borderColor: 'rgb(132, 255, 99)',
			stack: 'Stack 0',
            data: barchart_differenceduration
		}]		
    },
		options: { responsive: false,
				   scales: {xAxes: [{ stacked: true }], yAxes: [{ stacked: true }] }
				 }
	});

};
}());