<?php
 namespace OCA\Timesheet\Controller;

 use OCP\IRequest;
 use OCP\AppFramework\Controller;

 use OCP\AppFramework\Http;
 use OCP\AppFramework\Http\DataResponse;
 use OCP\AppFramework\Http\JSONResponse;
 
 use OCA\Timesheet\Service\ReportService;
 use OCA\Timesheet\Db\WorkRecord;
 //use OCA\Timesheet\Db\WorkReport;
 
 class SettingsController extends Controller {
	 
     // Private variables, which are necessary.
     private $userId;
	 private $Reportservice;
	 
	 protected $request;
	 	 
	 // ==================================================================================================================
	// Constructing this instance
     public function __construct(string $AppName, IRequest $request, $userId,
	 							 ReportService $service){
         parent::__construct($AppName, $request);
		 
		 // initialize variables
		 $this->request = $request;
		 $this->userId = $userId;
		 $this->Reportservice = $service;
     }
	 
	 // ==================================================================================================================	
     /**
      * @NoAdminRequired
      * 	  
      */
     public function change() {
		 
		 
		 
		 return new DataResponse("Hallo");

     }
	 
 }
