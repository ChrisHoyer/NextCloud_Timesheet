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
	   ['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
	   
	   // Modify records
	   ['name' => 'timesheet#create', 'url' => '/record', 'verb' => 'POST'],
	   ['name' => 'timesheet#delete', 'url' => '/record/{id}', 'verb' => 'DELETE'],
	   ['name' => 'timesheet#update', 'url' => '/record/{id}', 'verb' => 'PUT'],
	   	   	   
	   // show records
	   ['name' => 'timesheet#show', 'url' => '/record/{id}', 'verb' => 'GET'],	   
	   ['name' => 'timesheet#showAll', 'url' => '/records', 'verb' => 'GET'],
	   
	   // Modify report
	   ['name' => 'report#create', 'url' => '/report', 'verb' => 'POST'],

	   // show report
	   ['name' => 'report#showAllReports', 'url' => '/reports', 'verb' => 'GET'],
	   ['name' => 'report#show', 'url' => '/report/{id}', 'verb' => 'GET'],	   	   
    ]
];