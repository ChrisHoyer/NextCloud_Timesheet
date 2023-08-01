<?php
 namespace OCA\Timesheet\Controller;

 use OCP\IRequest;
 use OCP\AppFramework\Controller;

 use OCP\AppFramework\Http;
 use OCP\AppFramework\Http\DataResponse;
 use OCP\AppFramework\Http\JSONResponse;
 
 use OCA\Timesheet\Service\FrameworkService;
 use OCA\Timesheet\Service\ReportService;
 
 use OCA\Timesheet\Db\Report;
 
 class ReportController extends Controller {
	 
     // Private variables, which are necessary.
     private $userId;
	 private $service;
	 private $fwservice;
	 
	 protected $request;

	 // use Errors.php
	 use Errors;
	 
// ==================================================================================================================
	// Constructing this instance
     public function __construct(string $AppName, IRequest $request, $userId,
	 							 ReportService $service, FrameworkService $fwservice){
         parent::__construct($AppName, $request);
		 
		 // initialize variables
		 $this->request = $request;
		 $this->userId = $userId;
		 $this->service = $service;
		 $this->fwservice = $fwservice;
     }
	 
	 
// ==================================================================================================================	
     /**
      * @NoAdminRequired
      *  
      */
     public function createupdateReport() {
		 
		 // validation of record data
		$valid_report = $this->fwservice->validate_ReportReq($this->request, $this->userId); 
		 
		 /*
		if ( strpos($valid_report, "ERROR") == true) {
			// Error -> send it back to form
			return new DataResponse( $valid_data );
		}*/
		 		 
		// check if database entry exists 
		$existingID = $this->service->findMonYear($valid_report->monyearid, $this->userId);
		 		 		 
		// create new ID, if nothing found
		if (empty($existingID)){
			
			$serviceResponse = $this->service->create($valid_report, $this->userId);
			
		// ID found	
		} else {
			
			$serviceResponse = $this->service->update($existingID[0]->id, $valid_report, $this->userId);
		}
		 
		 // Refresh accumulated overtime in reports
		 $reportlist = $this->service->findAll($this->userId);
		 
		 return new DataResponse($reportlist);
		 
		 //$overtimeResponse = $this->fwservice->getOvertimeAcc($reportlist);
		 
		 //foreach ($overtimeResponse as $key => $value) {
		//	 $this->service->updateOvertimeAcc($key, $value, $this->userId);
		 //}		 
		 
		return new DataResponse($this->fwservice->clean_report($serviceResponse));

     }

	 // ==================================================================================================================	
     /**
      * @NoAdminRequired
      *  
      */
     public function signReport() {
		 	 
		// check if database entry for this month/year and user exists
		$existingID = $this->service->findMonYear($this->request->monyearid, $this->userId); 
		 		 		 
		// ID found?
		if (!empty($existingID)){
			
			// Change State
			$existingID[0]->signedoff = $existingID[0]->signedoff == "0" ? "1" : "0";
						
			// update
			$serviceResponse = $this->service->update($existingID[0]->id, $existingID[0], $this->userId);
		}
		 
		 
		return new DataResponse($serviceResponse);

     }

// ==================================================================================================================	
     /**
      * @NoAdminRequired
      * 
      */
     public function getReportlist() {
		 
		 // now find the id and show it			 
		 $reportlist = $this->service->findAll($this->userId);
		 
		 // show availible Reports
		 $serviceResponse = $this->fwservice->getReportList($reportlist);
	 
		 // get current month and year
		 $current_year = gmdate("Y");			
		 $current_month = gmdate("n");
		 
		 // check if current month/year is included
		 $existing_months = $serviceResponse["reports"][$current_year];
		 			 
		 if( empty($existing_months) or (!in_array($current_month, $existing_months))) {
			 
			 // get latest entry
			 $lastEntry = $this->service->getLastEntry($this->userId);
			 
			 // generate new Entry, if no last entry was found
			 if ( empty($lastEntry) ){
				 
				 $lastEntry = new Report();
				 $lastEntry->setmonyearid( $current_year . "," . $current_month );
				 
				 
			// use last entry and modify it for new entry (copy everything except ids)
			} else {
				
				// otherwise use last entry and modify it
				$lastEntry = $lastEntry[0];
				$lastEntry->setmonyearid( $current_year . "," . $current_month );
				$lastEntry->id = "";
				$lastEntry->user_id = $this->userId;									
			}
		 			 
		 	// insert new report
			$this->request = $lastEntry;
			$this->createupdateReport();
			
			// ask for all new reports
			$reportlist = $this->service->findAll($this->userId);
			$serviceResponse = $this->fwservice->getReportList($reportlist);		
				 
		} 
		 		 	 
		 // Return
		 return $serviceResponse;
     }
	 
// ==================================================================================================================	
     /**
      * @NoAdminRequired
      * 
      */
     public function getReport($year, $month) {
		 
		 // now find the id and show it			 
		 return $this->service->findMonYear(($year . "," . $month), $this->userId)[0];
     }		 
 }
