<?php
 namespace OCA\Timesheet\Controller;

 use OCP\IRequest;
 use OCP\AppFramework\Controller;

 use OCP\AppFramework\Http;
 use OCP\AppFramework\Http\DataResponse;
 use OCP\AppFramework\Http\JSONResponse;
 
 use OCA\Timesheet\Service\FrameworkService;
 use OCA\Timesheet\Service\ReportService;
 
 use OCA\Timesheet\Db\WorkReport;
 
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
     public function createupdate() {

		 // validation of record data
		$valid_report = $this->fwservice->validate_ReportSettings($this->request, $this->userId); 

		// check if database entry exists 
		$existingID = $this->service->findMonYear($valid_report->monyearid, $this->userId);
		
		// create new ID, if nothing found
		if (empty($existingID)){	
			$serviceResponse = $this->service->create($valid_report, $this->userId);
			return new DataResponse($this->fwservice->clean_report($serviceResponse));
			
		// ID found	
		} else {
			
			$serviceResponse = $this->service->update($existingID[0]->id, $valid_report, $this->userId);
			return new DataResponse($this->fwservice->clean_report($serviceResponse));
			
		}

     }


// ==================================================================================================================	
     /**
      * @NoAdminRequired
      * 
      */
     public function showAllReports() {
		 
		 // Report List for Drop Down Menü
		 $reportlist_decoded;	

		 // now find the id and show it			 
		 $reportlist = $this->service->findAll($this->userId);
		 $reportlist_decoded["resp1"] = $reportlist;		
		  
		// Generate default entry on existing records (startdate) of user
		if (!empty($reportlist)){	

			// Extract existing dates from records			
			foreach ($reportlist as &$report) {
				
				// Seperate Month and Year
				$MonYear = explode(",", $report->monyearid);
				$report_year = $MonYear[0];			
				$report_month = $MonYear[1];
				
			    $reportlist_decoded["resp"][$report_month] = $report_year;
				
				// Get all months from this year (empty if generated new)
				$existing_months = $reportlist_decoded["select"][$report_year];
				
				// check if months is empty, otherwise load
				if( empty($existing_months) )
					$existing_months = array($report_month);
				else {
					
					// check if month is included
					if (!in_array($report_month, $existing_months))
						array_push($existing_months, $report_month);					
				}
				
				// Write Back
				$reportlist_decoded["select"][$report_year] = $existing_months;				
								
			}
							
		}

		
		// check if current month is included
		$current_year = gmdate("Y");			
		$current_month = gmdate("F");	
		
		// Get all months from this year (empty if generated new)
		$existing_months = $reportlist_decoded["select"][$current_year];
		
		// check if months is empty, otherwise load
		if( empty($existing_months) )
			$existing_months = array($current_month);
		else {
			// check if month is included
			if (!in_array($current_month, $existing_months))
			array_push($existing_months, $current_month);					
		}
		
		// Write Back
		$reportlist_decoded["select"][$current_year] = $existing_months;			
		$reportlist_decoded["preselect_year"] = $current_year;
		$reportlist_decoded["preselect_month"] = $current_month;
				
		 // Return
		 return $reportlist_decoded;
     }		 
 }
