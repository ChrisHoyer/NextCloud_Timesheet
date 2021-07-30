<?php
 namespace OCA\Timesheet\Controller;

 use OCA\Timesheet\Service\FrameworkService;
 use OCA\Timesheet\Service\ReportService;
 use OCA\Timesheet\Service\RecordService;

 use OCP\IRequest;
 use OCP\AppFramework\Controller;
 
 use OCP\AppFramework\Http;
 use OCP\AppFramework\Http\TemplateResponse;
 use OCP\AppFramework\Http\DataResponse;
 use OCP\AppFramework\Http\JSONResponse;
 

 class TimesheetController extends Controller {

     // request information
     private $userId;
	 protected $request;
	 
	 // All Services
	 private $frameworkservice;
	 private $recordservice;
	 private $reportservice;
	 	 
	 // use Errors.php
	 use Errors;

		
// ==================================================================================================================
	// Constructing this instance with request, userID and services
     public function __construct(string $AppName, IRequest $request, $userId,
								 FrameworkService $frameworkservice,
								 ReportService $reportservice,
								 RecordService $recordservice)
	 {
         parent::__construct($AppName, $request);
		 
		 // initialize variables
		 $this->request = $request;
		 $this->userId = $userId;
		 
		 // initialize services
		 $this->frameworkservice = $frameworkservice;
		 $this->reportservice = $reportservice;
		 $this->recordservice = $recordservice;
     }

// ==================================================================================================================	
	/**
	 * This part generates the default landing page
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function index() {
		
		// landing page: get content, style and javascript
		return new TemplateResponse('timesheet', 'index',['appPage' => 'content/index', 'script' => 'timesheet', 'style' => 'timesheet']);
	}	 

// ==================================================================================================================	
     /**
      * This function is called, if a new record data request comes in. Either a new record should be created or an existing
	  * record will be updated. This depends on the the existing ID of this record. An empty ID triggers a record creation
	  *
	  * @NoAdminRequired
      */
     public function createupdateRecord() {

		// validation of record data
		$valid_data = $this->frameworkservice->validate_RecordReq($this->request, $this->userId); 
		
		if ( strpos($valid_data, "ERROR") == true) {
			// Error -> send it back to form
			return new DataResponse( $valid_data );
		}
			
		// check if database entry exists 
		$requestID = $this->request->id;
				
		if (!empty($requestID) ) {
			$existingID = $this->recordservice->find($requestID, $this->userId);
		} else {
			$existingID = "";
		}
		
		// create new ID, if nothing found
		if (empty($existingID)){	
			$serviceResponse = $this->recordservice->create($valid_data, $this->userId);
			
		// ID found	
		} else {
			// Use service to save the record data in a database			
			$serviceResponse = $this->recordservice->update($requestID, $valid_data, $this->userId);
		}
		
		// set recalc_required flag for corresponding report
		$reportID = gmdate("Y", $serviceResponse->startdatetime) . "," .gmdate("n", $serviceResponse->startdatetime); 
		$this->reportservice->setRecalcReportFlag($reportID, $this->userId);
					

		return new DataResponse($serviceResponse);
	}	
     

// ==================================================================================================================	
     /**
      * @NoAdminRequired
      * 
	  * @param int $id  
      */
     public function deleteRecord(int $id) {	
	 	// New Error Handler function, which calls the find function
		 return $this->handleNotFound(function () use ($id) {
		 // now find the id and show it			 
            return $this->recordservice->delete($id, $this->userId);
        });
     }

// ==================================================================================================================	
     /**
      * @NoAdminRequired
      * 
      */
     public function getRecordsRange($year, $month, $start, $end, $output) {
		 
		 		 
		 //	 Check if parameters for year and month are defined
		 if( !is_null($year) & !is_null($month) ) {
			 
			 // Generate timestemp for the first and last day of the month in UNIX time without offset
			 $firstday = strtotime(gmdate("Y-m-d", strtotime($year . "-" . $month . "-01")) . " 00:00");
			 $lastday = strtotime(gmdate("Y-m-t", strtotime($year . "-" . $month . "-01")) . " 23:59");
			 
		// check if parameters for startdate and enddate are defined
		 } elseif( !is_null($start) & !is_null($end) ) {
			 
			 // Generate timestemp for the first and last day from UNIX Time (in seconds!)
			 $firstday = $start;
			 $lastday = $end;
			 
		// bad request
		 } else {
			 
			 return "Error in Request";
		 }
			 		 
		// now find all entries from this month
		$recordlist = $this->recordservice->findAllRange($firstday, $lastday, $this->userId);
		 
		// get first element, cast to array and include first and last date
		$monthly_report_setting = $this->reportservice->findMonYear(($year . "," . $month), $this->userId);
		$monthly_report_setting = (array) $monthly_report_setting[0];
		 

		// check if first and last day is determined by report
		$report_firstday = ($monthly_report_setting["startreport"])?($monthly_report_setting["startreport"]):($firstday);
		$report_lastday = ($monthly_report_setting["endreport"])?($monthly_report_setting["endreport"]):($lastday);

		$monthly_report_setting["reportdatemax"] = gmdate("Y-m-t", $firstday);
		$monthly_report_setting["reportdatemin"] = gmdate("Y-m-d", $firstday);
		 
		// get date from last and start date (only full day counts)
		$monthly_report_setting["startreport"] = ($monthly_report_setting["startreport"])?(gmdate("Y-m-d", $monthly_report_setting["startreport"] . " 00:00")):(0);
		$monthly_report_setting["endreport"] = ($monthly_report_setting["endreport"])?(gmdate("Y-m-d", $monthly_report_setting["endreport"] . " 00:00")):(0);
		 
		 
		// get all days of this month (if current month, only days in past)
		if($lastday > time()) $lastday = time();
		if($report_lastday > time()) $report_lastday = time();
		 
		for($i=$report_lastday; $i>=$report_firstday; $i-=86400) {$daylist[] = date('Y-m-d', $i);}	 
		
		 // read records and cast into format for jquery
		if($output == "report"){
			
			 $recordlist_table = $this->frameworkservice->map_record2report($recordlist, $daylist, $monthly_report_setting);
			 $recordlist_table["daylist"] = $daylist;
			 $recordlist_table["first_last"] = $report_firstday . " to " . $report_lastday;
						
			 // update Report based on Records
			 $this->reportservice->updateReport($recordlist_table["summary"], $this->userId);
			

			 
		} elseif($output == "list"){
			 $recordlist_table = $this->frameworkservice->map_record2day($recordlist, $daylist);
		}
		
		// some infos about search request
		$recordlist_table["requested"] = "Search between " .$firstday . " and " . $lastday;
		$recordlist_table["requested"] = "year=" . $year . " month=" . $month . " start=" . $start . " end=" . $end . " output=" . $output;
				
		 // Return
		 return $recordlist_table;
		 
		
		 
		 
     }	 

 }