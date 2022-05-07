// ==========================================================================================================================================================================
// ================================================================================= Functional Events ======================================================================
(function() {

	// ===================== After Pageload
	document.addEventListener('DOMContentLoaded', function() { RefreshProject(); });
	
	// ===================== Submit Button click
	$("#timesheet-newproject-submit").click(function() {
		
		// gather all required data from form
		var request_data = {
			
				// Record ID
				id: "",
				
				// Project and Description
				projectname: $('#timesheet-newproject-name').val(),
				description: $('#timesheet-newproject-description').val(),
				parentid: $('#timesheet-newrecord-selectionbox-project').val(),
							
				// Additional stuff which is not implemented in frontend	
			};
			
		
		// POST request with all data
		createupdateProject(request_data, RefreshProject());
		
	});
	
	// ===================== Refresh Button click
	$("#timesheet-newproject-refresh").click(function() { RefreshProject(); });
	
// ======================================================================================================================================================================
// ================================================================================= Static Events ======================================================================

// ===================== Load Projects for this user 
function RefreshProject() {
		
		// Request Projects from this user
		getTopProjectList(generateProjectSelection);
	
		// Request Projects from this user
		getProjects(generateProjectTable);

}

// ========================================================================================================================================================================
// ================================================================================= Page Generating ======================================================================

// ===================== Generates Table from Projects JSON Response
function generateProjectTable(projectlist){
	
	// table content
	var content = [];
			
	// ===================== Generate Record Table ===================	
	
	// Iterate all project items in List
	$.each(projectlist, function (index, entity){
		
		// create table for current entry top project header
		var project = [];
		
		project = "<div class='timesheet_tablerowmerged timesheet-project' >";
		project += "<div class='timesheet-tablerowhover timesheet-topproject' >";
		project	+= "<div class='timesheet-tablecell timesheet-tablecolumn-short'> </div>";
		project	+= "<div class='timesheet-tablecell timesheet-tablecolumn-long timesheet-topproject-name'>" + entity.projectname + "</div>";
		project	+= "<div class='timesheet-tablecell timesheet-tablecolumn-long'>" + entity.description + "</div>";
		project	+= "<div class='timesheet-tablecell timesheet-tablecolumn-time'>" + entity.timeplanned + "</div>";
		project	+= "<div class='timesheet-tablecell timesheet-tablecolumn-time'>" + entity.timespend + "</div>";
		
		// Edit
		project += "<div class='timesheet-tablecell timesheet-tablecolumn-modify'>";
		project += "<span class='timesheet-project-delete icon-delete' data-dbid=" + entity.id + "></span>";
		project += "<span class='timesheet-project-edit icon-rename' data-projectname='" + entity.projectname + "'";
		project += " data-description='" + entity.description + "' data-plannedduration='" + entity.timeplanned + "'></span>";
		project += "</div>";
		
		project += "</div>";
		
		// Iterate all subproject items in List
		$.each(entity.child, function (subindex, subentity){
			
			project += "<div class='timesheet-tablerowhover timesheet-subproject' >";
			project	+= "<div class='timesheet-tablecell timesheet-tablecolumn-short'> <span>&rarr;</span> </div>";
			project	+= "<div class='timesheet-tablecell timesheet-tablecolumn-long'>" + subentity.projectname + "</div>";
			project	+= "<div class='timesheet-tablecell timesheet-tablecolumn-long'>" + subentity.description + "</div>";
			project	+= "<div class='timesheet-tablecell timesheet-tablecolumn-time'>" + subentity.timeplanned + "</div>";
			project	+= "<div class='timesheet-tablecell timesheet-tablecolumn-time'>" + subentity.timespend + "</div>";	
			
			// Edit
			project += "<div class='timesheet-tablecell timesheet-tablecolumn-modify'>";
			project += "<span class='timesheet-project-delete icon-delete' data-dbid=" + subentity.id + "></span>";
			project += "<span class='timesheet-project-edit icon-rename' data-projectname='" + subentity.projectname + "'";
			project += " data-description='" + subentity.description + "' data-plannedduration='" + subentity.timeplanned + "'></span>";
			project += "</div>";	
			
			project += "</div>";
			
		});
		
		project	+= "</div>";
				
		// Include into Table
		content.push(project.toString());
					
	});
	
	
	// ===================== Display HTML Table ===================
	$("#timesheet-projects-content").html($( "<div/>", { "class": "timesheet-projects-content-generated", html: content.join( "" )}));
	
	// ===================== Delete Click ===================
	$(".timesheet-project-delete").click(function(e) {
		
		// Ask for permission
	 	var result = confirm( "Delete ID: " + $(e.target).data("dbid") + " ?");
		
		if (result == true) { deleteProjectID($(e.target).data("dbid")); }
	
	});

	// ===================== Edit Click ===================
	$(".timesheet-project-edit").click(function(e) {
		
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
					
}	
	
	
// ===================== Generates Selectionbox from Projectlist JSON Response
function generateProjectSelection(projectlist){
	
		// Generate HTML code for Report Header
		var ProjectSelection = [];
		
		// Generate table row content
		ProjectSelection = "<div class='timesheet-newproject-parentselection-content' >";
		ProjectSelection += "<select id='timesheet-newrecord-selectionbox-project' name='project' class='timesheet-selectionbox' >";	
	
		// unassigned project
		ProjectSelection += "<option selected='selected' value=\"0\" > - unassigned - </option>";
			
		// Iterate all entries
		$.each(projectlist, function (id, name){ ProjectSelection += "<option value=\"" + id +"\" >" + name + "</option>";});
				
		ProjectSelection += "</select></div>";
				
		// Include Selectionbox
		$("#timesheet-newproject-parentselection").html($( "<div/>", {"class": "timesheet-project-selectionbox-generated", html: ProjectSelection.toString() }));
								
}

// ===================== Close Function
}());