<?php
 namespace OCA\Timesheet\Controller;

 use OCP\IRequest;
 use OCP\AppFramework\Controller;

 use OCP\AppFramework\Http;
 use OCP\AppFramework\Http\DataResponse;
 use OCP\AppFramework\Http\JSONResponse;
 
 use OCA\Timesheet\Service\ReportService;
 use OCA\Timesheet\Db\WorkRecord;
 use OCA\Timesheet\Db\WorkReport;
 
 class SettingsController extends Controller {
	 
     // Private variables, which are necessary.
     private $userId;
	 private $Reportservice;
	 
	 protected $request;

// ==================================================================================================================	
	// Check record data from request
	private function validate_WorkReport(){

		 // create instance of database class
		 $report = new WorkReport();
		 $report->setUserId($this->userId);
		 		 
		 // Weekly Hours and Days
		 $report->setRegularweeklyhours($this->request->regularweeklyhours);
		 $RegularDays = $this->request->workingdDayMon . "," . $this->request->workingdDayTue . "," . $this->request->workingdDayWed . ","; 
		 $RegularDays = $RegularDays . $this->request->workingdDayThu . "," . $this->request->workingdDayFri . ",";	
		 $RegularDays = $RegularDays . $this->request->workingdDaySat . "," . $this->request->workingdDaySun;			 
		 $report->setRegulardays($RegularDays);

		 // return ok
		 return $report;
		 		
	}
	 	 
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
		 
		 // validation of record data
		$valid_report = $this->validate_WorkReport(); 
		 
		 return new DataResponse($valid_report);

     }
	 
 }
