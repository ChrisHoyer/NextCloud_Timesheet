// default app URL
var baseUrl = OC.generateUrl('/apps/timesheet');


// ============================== Functional Events ===================
(function() {

// ===================== Submit Click ===================	
// Submit Button click
$("#submit").click(function() {

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
            description: "",
            timezoneoffset: new Date().getTimezoneOffset()
		};
		
	//alert(JSON.stringify(record_data));
	
	// POST request with all data
	$.post(record_url, record_data, function() {})
			.done(function(data, status) {
				var response = data
					alert(response);
			})
			.fail(function() {
				alert( "error" );
			})
			.always(function() {
			});
	});

// ===================== Refresh Click ===================
// Refresh Button click
$("#refresh").click(function() {

	// Request using POST at /record
	var record_url = baseUrl + '/allrecords';
	
	// GET request with all data from userID
	$.getJSON(record_url, function() {})
			.done(function(data, status) {
				var response = data;
				generateRecordList(response);	
			})
			.fail(function() {
				alert( "error" );
			})
			.always(function() {
			});
				
	});

// ===================== Refresh Click ===================
      function escapeHtml (string) {
        return String(string).replace(/[&<>"'`=\/]/g, function (s) {
          return entityMap[s];
        });
      }
	  

// ============================== Static Events ===================
// Generates Table from Recordlist JSON Response
function generateRecordList(recordlist){
	
	// table content
	var record_table = [];
	
	// Iterate all items in List
	$.each(recordlist, function (record_index, record_entity){
		
		// table row
		var record_table_row = [];
		
		// Generate table row content
		record_table_row.push("<div class='timesheet-record-table-row' data-myid=" + record_entity.id + ">");
		// Generate first column with Title
		record_table_row.push("<div class='timesheet-record-table-row-title'>" + escapeHtml(record_entity.title) + "</div>");
		// End Table Row
		record_table_row.push("</div>");		
		
		// Include into Table
		record_table.push(record_table_row);
		
	});
	
	alert(JSON.stringify(record_table));					

};


	// ajax call (with all data)
	/*$.ajax({
		url: baseUrl + '/record',
		type: 'POST',
		contentType: 'application/json',
		data: JSON.stringify(note)
	}).done(function (response) {
		// handle success
		alert(JSON.stringify(response));
		
		var nodeId = 666;
		$.get(baseUrl + '/record/' + nodeId, function( response )
		{ alert( response );
		});
		
	}).fail(function (response, code) {
		// handle failure
		alert(code + response);
	});*/
		
// Open Datepicker for single Date
/*$(function() {
  $('input[name="date"]').daterangepicker({
    singleDatePicker: true,
    showDropdowns: true,
    maxDate: moment().format('DD/MM/YYYY'),
  });
});*/



/* // ajax call (jquery function)
$.ajax({
    url: baseUrl + '/record',
    type: 'POST',
    contentType: 'application/json',
    data: JSON.stringify(note)
}).done(function (response) {
    // handle success
	alert(JSON.stringify(response));
	
	var nodeId = 666;
	$.get(baseUrl + '/record/' + nodeId, function( response )
	{ alert( response );
	});
	
}).fail(function (response, code) {
    // handle failure
	alert(code + response);
}); */

}());