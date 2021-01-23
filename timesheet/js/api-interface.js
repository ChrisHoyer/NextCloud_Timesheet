// default app URL
var baseUrl = OC.generateUrl('/apps/timesheet');

// ============================================================================
// ============================== API Interface for Records ===================

// ===================== Delete Record by ID for this user
function deleteRecordID(recordID) {

	// Request a Delete at /record/{id}
	var record_url = baseUrl + '/record/' + recordID;
		
	// DELETE request with entity ID
	$.ajax({
		headers: {requesttoken: oc_requesttoken},
		url: record_url,
		dataType: 'json',	
		type: 'DELETE'})
		.done(function () {	
			//alert( "Deleted: " + JSON.stringify(response) );
		 });
}


// ============================================================================
// ============================== Utilities ===================================

function extractDate(datestring) {
	
	// convert to date
	var inputdate = new Date(datestring)
	
	// reduce timezone offset
	var timezone = inputdate.getTimezoneOffset()
	inputdate = new Date(inputdate.getTime() - (timezone*60*1000))
	
	// return date
	return inputdate.toISOString().split('T')[0]	
}