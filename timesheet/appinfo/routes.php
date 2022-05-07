<?php
/**
 * Create your routes in here. The name is the lowercase name of the controller
 * without the controller part, the stuff after the hash is the method.
 * e.g. page#index -> OCA\Timesheet\Controller\PageController->index()
 *
 * The controller class has to be registered in the application.php file since
 * it's instantiated in there
 */
return [
    'routes' => [
	
		// pages
	   ['name' => 'timesheet#index', 'url' => '/', 'verb' => 'GET'],
	   ['name' => 'calendar#index', 'url' => '/calendar', 'verb' => 'GET'],
	   ['name' => 'project#index', 'url' => '/project', 'verb' => 'GET'],
		
	   // Timesheet
	   ['name' => 'timesheet#getRecordsRange', 'url' => '/getrecords', 'verb' => 'GET'],
	   ['name' => 'timesheet#createupdateRecord', 'url' => '/record', 'verb' => 'POST'],
	   ['name' => 'timesheet#deleteRecord', 'url' => '/record/{id}', 'verb' => 'DELETE'],
	   	   	     	   
	   // Records
	   ['name' => 'report#getReportlist', 'url' => '/getreportlist', 'verb' => 'GET'],
	   ['name' => 'report#getReport', 'url' => '/getreport', 'verb' => 'GET'],
	   ['name' => 'report#createupdateReport', 'url' => '/report', 'verb' => 'POST'],
	   ['name' => 'report#signReport', 'url' => '/signreport', 'verb' => 'POST'],
		
		// Projects
	   ['name' => 'project#getProjectlist', 'url' => '/getprojectlist', 'verb' => 'GET'],
	   ['name' => 'project#getTopProjectlist', 'url' => '/gettopprojectlist', 'verb' => 'GET'],
	   ['name' => 'project#getProjects', 'url' => '/getprojects', 'verb' => 'GET'],	
	   ['name' => 'project#createupdateProject', 'url' => '/project', 'verb' => 'POST'],
	   ['name' => 'project#deleteProject', 'url' => '/project/{id}', 'verb' => 'DELETE'],		
    ]
];
