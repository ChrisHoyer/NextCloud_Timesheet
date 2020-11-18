// default app URL
var baseUrl = OC.generateUrl('/apps/timesheet');

// basic note for data
var note = {
    title: 'New note',
    content: 'This is the note text'
};


// ajax call (jquery function)
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
});