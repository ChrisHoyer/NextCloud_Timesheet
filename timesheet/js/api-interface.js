// default app URL
var baseUrl = OC.generateUrl('/apps/timesheet');

// ====================================================================================================================================================
// ====================================================== Global Constants  ===========================================================================
const timesheet_MonthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

// ====================================================================================================================================================
// ================================================= API Interface for Records and Reports ============================================================

// ===================== Get Report by ID for this user
function getReport(year, month, successCallback, failureCallback) {
	
	// Request using POST at /getrecords with accepted arguments year, month or start, end
	var request_url = baseUrl + "/getreport?year=" + year + "&month=" + month;
		 
	// GET request with all data from userID
	$.ajax({
			headers: {requesttoken: oc_requesttoken},
			url: request_url,
			dataType: 'json',	
			type: 'GET', 
			})
			// Callbacks
			.success(function(data, status) { successCallback(data); })
			.fail(function(reponse) { failureCallback(response); })
}

// ===================== CREATE or UPDATE Report by ID for this user
function createupdateReport(request_data, successCallback, failureCallback) {
	
	// Request using POST at /getrecords with accepted arguments year, month or start, end
	var request_url = baseUrl + "/report";
		 
	// GET request with all data from userID
	$.ajax({
			headers: {requesttoken: oc_requesttoken},
			url: request_url,
			data: request_data,
			dataType: 'json',	
			type: 'POST', 
			})
			// Callbacks
			.success(function(data, status) { successCallback(data); })
			.fail(function(reponse) { failureCallback(response); })
}

// ===================== SIGN Report by ID for this user
function signReport(request_data, successCallback, failureCallback) {
	
	// Request using POST at /getrecords with accepted arguments year, month or start, end
	var request_url = baseUrl + "/signreport";
		 
	// GET request with all data from userID
	$.ajax({
			headers: {requesttoken: oc_requesttoken},
			url: request_url,
			data: request_data,
			dataType: 'json',	
			type: 'POST', 
			})
			// Callbacks
			.success(function(data, status) { successCallback(data); })
			.fail(function(reponse) { failureCallback(response); })
}

// ===================== GET complete Reportlist from User
function getReportList(successCallback, failureCallback) {
	
	// Request using POST at /record
	var request_url = baseUrl + '/getreportlist';
		
	// GET request with all reports from userID
	$.ajax({
			headers: {requesttoken: oc_requesttoken},
			url: request_url,
			dataType: 'json',	
			type: 'GET',
			})
			// Callbacks
			.success(function(data, status) { successCallback(data); })
			.fail(function(reponse) { failureCallback(response); })
}

// ===================== GET records in range
function getRecords(year, month, start, end, output, successCallback, failureCallback) {
		
	// Request using POST at /getrecords with accepted arguments year, month or start, end
	var request_url = baseUrl + "/getrecords?year=" + year + "&month=" + month;
	request_url = request_url + "&start=" + start + "&end=" + end + "&output=" + output;
		 
	// GET request with all data from userID
	$.ajax({
			headers: {requesttoken: oc_requesttoken},
			url: request_url,
			dataType: 'json',	
			type: 'GET', 
			})
			// Callbacks
			.success(function(data, status) { successCallback(data); })
			.fail(function(reponse) { failureCallback(response); })
}

// ===================== CREATE or UPDATE record for this user
function createupdateRecord(request_data, successCallback, failureCallback) {

	// Request a PUT at /record/{id}
	var request_url = baseUrl + '/record';
			
	// PUT request with entity ID and new data
	$.ajax({
			headers: {requesttoken: oc_requesttoken},
			url: request_url,
			data: request_data,
			dataType: 'json',
			type: 'POST', 
			})
			// Callbacks
			.success(function(data, status) { successCallback(data); })
			.fail(function(reponse) { failureCallback(response); })

}

// ===================== DELETE Record by ID for this user
function deleteRecordID(recordID, successCallback, failureCallback) {

	// Request a Delete at /record/{id}
	var request_url = baseUrl + '/record/' + recordID;
		
	// DELETE request with entity ID
	$.ajax({
			headers: {requesttoken: oc_requesttoken},
			url: request_url,
			dataType: 'json',	
			type: 'DELETE'})
			// Callbacks
			.success(function(data, status) { successCallback(data); })
			.fail(function(reponse) { failureCallback(response); })
}

// ====================================================================================================================================================
// ================================================= API Interface for Projects =======================================================================

// ===================== GET complete Projectlist from User
function getProjectList(successCallback, failureCallback) {
	
	// Request using POST at /record
	var request_url = baseUrl + '/getprojectlist';
		
	// GET request with all reports from userID
	$.ajax({
			headers: {requesttoken: oc_requesttoken},
			url: request_url,
			dataType: 'json',	
			type: 'GET',
			})
			// Callbacks
			.success(function(data, status) { successCallback(data); })
			.fail(function(reponse) { failureCallback(response); })
}

// ===================== GET complete Projectlist from User
function getTopProjectList(successCallback, failureCallback) {
	
	// Request using POST at /record
	var request_url = baseUrl + '/gettopprojectlist';
		
	// GET request with all reports from userID
	$.ajax({
			headers: {requesttoken: oc_requesttoken},
			url: request_url,
			dataType: 'json',	
			type: 'GET',
			})
			// Callbacks
			.success(function(data, status) { successCallback(data); })
			.fail(function(reponse) { failureCallback(response); })
}

// ===================== GET complete Projectlist from User
function getProjects(successCallback, failureCallback) {
	
	// Request using POST at /record
	var request_url = baseUrl + '/getprojects';
		
	// GET request with all reports from userID
	$.ajax({
			headers: {requesttoken: oc_requesttoken},
			url: request_url,
			dataType: 'json',	
			type: 'GET',
			})
			// Callbacks
			.success(function(data, status) { successCallback(data); })
			.fail(function(reponse) { failureCallback(response); })
}

// ===================== CREATE or UPDATE Project for this user
function createupdateProject(request_data, successCallback, failureCallback) {

	// Request a PUT at /project/{id}
	var request_url = baseUrl + '/project';
			
	// PUT request with entity ID and new data
	$.ajax({
			headers: {requesttoken: oc_requesttoken},
			url: request_url,
			data: request_data,
			dataType: 'json',
			type: 'POST', 
			})
			// Callbacks
			.success(function(data, status) { successCallback(data); })
			.fail(function(reponse) { failureCallback(response); })

}

// ===================== DELETE Project by ID for this user
function deleteProjectID(recordID, successCallback, failureCallback) {

	// Request a Delete at /record/{id}
	var request_url = baseUrl + '/project/' + recordID;
		
	// DELETE request with entity ID
	$.ajax({
			headers: {requesttoken: oc_requesttoken},
			url: request_url,
			dataType: 'json',	
			type: 'DELETE'})
			// Callbacks
			.success(function(data, status) { successCallback(data); })
			.fail(function(reponse) { failureCallback(response); })
}

//

// ====================================================================================================================================================
// ====================================================== Utilities ===================================================================================

function extractDate(datestring) {
	
	// convert to date
	var inputdate = new Date(datestring)
	
	// reduce timezone offset
	var timezone = inputdate.getTimezoneOffset()
	inputdate = new Date(inputdate.getTime() - (timezone*60*1000))
	
	// return date
	return inputdate.toISOString().split('T')[0]	
}