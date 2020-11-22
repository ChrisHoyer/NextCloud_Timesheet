// default app URL
var baseUrl = OC.generateUrl('/apps/timesheet');

// ==========================================================================================
// ============================== Functional Events ===================
(function() {

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
            close: function() { form[ 0 ].reset();}
          });
		  
// ===================== Load Page ===================	
	// get Records from this user
	getRecordList();

// ===================== Submit Button click
$("#timesheet-newrecord-submit").click(function() {

	// Request using POST at /record
	var record_url = baseUrl + '/record';
	
	// gather all required data from form
	var record_data = {
			// Start/End Time and Date
            startdate: $('#timesheet-newrecord-date').val(),
			enddate: $('#timesheet-newrecord-date').val(),
			
            starttime: $('#timesheet-newrecord-starttime').val(),
            endtime: $('#timesheet-newrecord-endtime').val(),
			// Breaktime
			breaktime: $('#timesheet-newrecord-breaktime').val(),
			// Additional stuff like description and timezone
            description: $('#timesheet-newrecord-description').val(),
            timezoneoffset: new Date().getTimezoneOffset(),
			holiday: 			"false",	
			vacation: 			"false",
			unpayedoverhours: 	"false",	
		};
		
	//alert(JSON.stringify(record_data));
	
	// POST request with all data
	$.post(record_url, record_data, function() { })
			.done(function(data, status) {
				//var response = data;
				//alert(response);
				
				// Info and refresh
				getRecordList();
	
			})
			.fail(function() {
				alert( "error" );
			})
			.always(function() {
			});
	});

// ===================== Refresh Click
// Refresh Button click
$("#timesheet-newrecord-refresh").click(function() {
	
	// get Records from this user
	getRecordList();
		
	});
	  


// ==========================================================================================
// ============================== Static Events/Functions ===================

// ===================== Delete Records for this user
function editRecord(dialogModifyRecordForm) {

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

// ===================== Load Records for this user
function getRecordList() {
	
	// Request using POST at /record
	var record_url = baseUrl + '/records';
	
	// GET request with all data from userID
	$.getJSON(record_url, function() {})
			.done(function(data, status) {
				
				// Generate Table
				var response = data;
				generateRecordList(response);
										
			})
			.fail(function() {
				alert( "error" );
			})
			.always(function() {
			});	
	
}

// ===================== Generates Table from Recordlist JSON Response
function generateRecordList(recordlist){
	
	// table content
	var record_table = [];
	
	// Iterate all items in List
	$.each(recordlist, function (record_index, record_entity){
		
		// table row
		var record_table_row = [];
		
		// Generate table row content
		record_table_row = record_table_row + "<div class='timesheet-record-table-row' entityid=" + record_entity.id + ">";

		// Generate first column of object
		record_table_row = record_table_row + "<div class='timesheet-record-table-row-cell timesheet-record-table-column-date'>" + record_entity.startday + ", " + record_entity.startdate + "</div>";		
		record_table_row = record_table_row + "<div class='timesheet-record-table-row-cell timesheet-record-table-column-start'>" + record_entity.starttime + "</div>";
		record_table_row = record_table_row + "<div class='timesheet-record-table-row-cell timesheet-record-table-column-end'>" + record_entity.endtime + "</div>";
		record_table_row = record_table_row + "<div class='timesheet-record-table-row-cell timesheet-record-table-column-break'>" + record_entity.breaktime + "</div>";
		record_table_row = record_table_row + "<div class='timesheet-record-table-row-cell timesheet-record-table-column-duration'>" + record_entity.recordduration + "</div>";		
						
		// Generate clickable trash can (trash can icon implemented by nextcloud env, https://docs.nextcloud.com/server/15/developer_manual/design/icons.html)
		// Generate clickable edit (edit icon implemented by nextcloud env, https://docs.nextcloud.com/server/15/developer_manual/design/icons.html)
		record_table_row = record_table_row + "<div class='timesheet-record-table-row-cell timesheet-record-table-column-modify'>";
		record_table_row = record_table_row + "<span class='timesheet-record-delete icon-delete' id="+record_entity.id+"></span>";
		record_table_row = record_table_row + "<span class='timesheet-record-edit icon-rename' id=" + record_entity.id + " data-startdate='" + record_entity.startdate + "'";
		record_table_row = record_table_row + " data-starttime='" + record_entity.starttime + "' data-endtime='" + record_entity.endtime + "' data-description='" + record_entity.description + "'";
		record_table_row = record_table_row + " data-holiday='" + record_entity.holiday + "' data-vacation='" + record_entity.vacation + "' data-unpayedoverhours='" + record_entity.unpayedoverhours + "'";
		record_table_row = record_table_row + " data-breaktime='" + record_entity.breaktime + "' data-dbid=" + record_entity.id + " ></span></div>";	
		
		// Description
		record_table_row = record_table_row + "<div class='timesheet-record-table-row-cell timesheet-record-table-column-description'>" + record_entity.description + "</div>";
			
		// End Table Row
		record_table_row = record_table_row + "</div>";
		
		
		// Include into Table
		record_table.push(record_table_row.toString());
		
	});
	
	// ===================== Display HTML Table ===================
	$("#timesheet-record-table-content").html($( "<div/>", {
                      "class": "timesheet-record-table-content-generated",
                      html: record_table.join( "" )
                    }));
					
	// ===================== Delete Click ===================
	$(".timesheet-record-delete").click(function(e) {
		
		// Ask for permission
	 	var result = confirm( "Delete ID: " + e.target.id + " ?");
		
		if (result == true) {
			deleteRecord(e.target.id);
		}
	
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
		form.find("#timesheet-dialog-holiday").attr( 'checked', $(e.target).data("holiday") );
		form.find("#timesheet-dialog-vacation").attr( 'checked', $(e.target).data("vacation") );
		form.find("#timesheet-dialog-unpayedoverhours").attr( 'checked', $(e.target).data("unpayedoverhours") );															
		// Open dialog form
		dialogModifyRecordForm.dialog("open");
		
	
	});				

};


}());