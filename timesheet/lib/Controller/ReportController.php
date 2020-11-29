<?php
 namespace OCA\Timesheet\Controller;

 use OCP\IRequest;
 use OCP\AppFramework\Controller;

 use OCP\AppFramework\Http;
 use OCP\AppFramework\Http\DataResponse;
 use OCP\AppFramework\Http\JSONResponse;
 
 use OCA\Timesheet\Service\TimesheetService;
 use OCA\Timesheet\Service\ReportService;
 use OCA\Timesheet\Db\WorkRecord;
 use OCA\Timesheet\Db\WorkReport;
 
 class ReportController extends Controller {
	 
     // Private variables, which are necessary.
     private $userId;
	 private $RPservice;
	 private $TSservice;
	 
	 protected $request;

	 // use Errors.php
	 use Errors;
	 
// ==================================================================================================================	
	// Check record data from request
	private function validate_WorkReport(){

		 // create instance of database class
		 $report = new WorkReport();
		 $report->setUserId($this->userId);
		 $report->setmonyearid($this->request->monyearid);
 		 		 
		 // Weekly Hours and Days
		 $report->setRegularweeklyhours($this->request->regularweeklyhours);
		 $RegularDays = $this->request->workingdDayMon . "," . $this->request->workingdDayTue . "," . $this->request->workingdDayWed . ","; 
		 $RegularDays = $RegularDays . $this->request->workingdDayThu . "," . $this->request->workingdDayFri . ",";	
		 $RegularDays = $RegularDays . $this->request->workingdDaySat . "," . $this->request->workingdDaySun;			 
		 $report->setRegulardays($RegularDays);
		 
		 // no data
		 $report->setVacation(0);
		 $report->setActualhours(0);
		 $report->setTargethours(0);
		 $report->setOvertimepayed(0);
		 $report->setOvertimeunpayed(0);
		 $report->setOvertimecompensation(0);
			 
		 // return ok
		 return $report;
		 		
	}
	
	// calculate actual, taget and overtime hours of this months report
	private function calc_hours(){
		
		



		 // return ok
		 return $summary;
		 		
	}

// ==================================================================================================================
	// tidy up record data from return
	private function clean_report($response){
			
		$reportlist_decoded;
		
		// Seperate Dates
		$reportlist_decoded["regularweeklyhours"] = $response->regularweeklyhours;

		
		// return
		return $reportlist_decoded;
		
		
	}
		 	 
	 // ==================================================================================================================
	// Constructing this instance
     public function __construct(string $AppName, IRequest $request, $userId,
	 							 ReportService $RPservice, TimesheetService $TSservice){
         parent::__construct($AppName, $request);
		 
		 // initialize variables
		 $this->request = $request;
		 $this->userId = $userId;
		 $this->RPservice = $RPservice;
		 $this->TSservice = $TSservice;
     }
	 
	 
// ==================================================================================================================	
     /**
      * @NoAdminRequired
      *  
      */
     public function createupdate() {

		 // validation of record data
		$valid_report = $this->validate_WorkReport(); 

		// check if database entry exists 
		$existingID = $this->RPservice->findMonYear($valid_report->monyearid, $this->userId);
		
		// create new ID, if nothing found
		if (empty($existingID)){	
			$serviceResponse = $this->RPservice->create($valid_report, $this->userId);
			return new DataResponse($this->clean_report($serviceResponse));
			
		// ID found	
		} else {
			
			$serviceResponse = $this->RPservice->update($existingID[0]->id, $valid_report, $this->userId);
			return new DataResponse($this->clean_report($serviceResponse));
			
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
		 $reportlist = $this->RPservice->findAll($this->userId);
		
		// No values availible?
			
		// Generate default entry on existing records (startdate) of user
		if (empty($reportlist)){
			
			$recordlist = $this->TSservice->read_existingdates($this->userId);
			
			// Extract existing dates from records			
			foreach ($recordlist as &$record) {
				
				// extract year and month
				$record_year = gmdate("Y", $record->startdatetime);			
				$record_month = gmdate("F", $record->startdatetime);
				
				// Get all months from this year (empty if generated new)
				$existing_months = $reportlist_decoded["select"][$record_year];
				
				// check if months is empty, otherwise load
				if( empty($existing_months) )
					$existing_months = array($record_month);
				else {
					
					// check if month is included
					if (!in_array($record_month, $existing_months))
						array_push($existing_months, $record_month);					
				}
				
				// Write Back
				$reportlist_decoded["select"][$record_year] = $existing_months;

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
