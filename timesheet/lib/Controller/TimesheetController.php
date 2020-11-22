<?php
 namespace OCA\Timesheet\Controller;

 use OCP\IRequest;
 use OCP\AppFramework\Controller;

 use OCP\AppFramework\Http;
 use OCP\AppFramework\Http\DataResponse;
 use OCP\AppFramework\Http\JSONResponse;
 
 use OCA\Timesheet\Service\TimesheetService;
 use OCA\Timesheet\Db\WorkRecord;
 
 class TimesheetController extends Controller {

     // Private variables, which are necessary.
     private $userId;
	 private $service;
	 
	 protected $request;
	 
	 // use Errors.php
	 use Errors;

// ==================================================================================================================	
	// Check record data from request
	private function validate_recorddate(){

		 // create instance of database class
		 $record = new WorkRecord();
		 $record->setUserId($this->userId);
		 
		 // Check user input: starttime/startdate before endtime/enddate
		 if (isset($this->request->starttime) & isset($this->request->startdate) & isset($this->request->endtime) & isset($this->request->enddate) ) {
			 
			 							
			 	// Get complete Start and End time for calculation of Duration in UNIX time
			 	$record->setStartdatetime( strtotime( $this->request->starttime . " " . $this->request->startdate ) );
				$record->setEnddatetime(  strtotime( $this->request->endtime . " " . $this->request->enddate ) );
				
				// Calculate complete Duration in seconds
			 	$t_completeduration = $record->enddatetime - $record->startdatetime;
				
				// Todo: If Negative, Error Message
				if ($t_completeduration < 0)
				{
					return "ERROR - invalid start-end data: " . $record->enddatetime . " - " . $record->startdatetime;
				}
				
				// Include Breaktime, if available (otherwise UNIX std time) 
				$record->setBreaktime( strtotime( "00:00 1970-01-01" ) );
				if(isset($this->request->breaktime)){
					$record->setBreaktime( strtotime( $this->request->breaktime . " 1970-01-01" ) );
				}
				
				// Calculate Working duration
			 	$t_workingduration = $t_completeduration - $record->breaktime;
				
				// Todo: If Negative, Error Message								
				if ($t_workingduration < 0)
				{
					return "ERROR - invalid break data: " . $record ;
				}
				
				// Save Break and Duration
				$record->setRecordduration(gmdate("H:i", $t_workingduration));
		 }
		 
		 // Regular Hours Availible?
		 $record->setRegularhours(0);
		 
		 
		 // Get all Flags and convert to integer
		 $record->setHoliday( $this->request->holiday );
		 $record->setVacation( $this->request->vacation );
		 $record->setUnpayedoverhours( $this->request->unpayedoverhours );		 		 

		 // Set additional Information
		 $record->setDescription($this->request->description);
		 $record->setTimezoneoffset($this->request->timezoneoffset);
		 
		 // Default Value for Tags and Projects
		 $record->setTags("");
		 $record->setProjects("");		 
		 
		 
		 // return ok
		 return $record;
		 		
	}

// ==================================================================================================================
	// tidy up record data from request
	private function read_recorddates($recordlist){
			
		$recordlist_decoded;		
			
		// Split into Groups for each Month
		foreach ($recordlist as &$record) {

			$record_decoded["id"] = $record->id;
			$record_decoded["startday"] = gmdate("D", $record->startdatetime);				
			$record_decoded["startdate"] = gmdate("Y-m-d", $record->startdatetime);			
			$record_decoded["starttime"] = gmdate("H:i", $record->startdatetime);
			$record_decoded["endtime"] = gmdate("H:i", $record->enddatetime);
			$record_decoded["breaktime"] = gmdate("H:i", $record->breaktime);
			
			$record_decoded["holiday"] = $record->holiday;
			$record_decoded["vacation"] = $record->vacation;
			$record_decoded["unpayedoverhours"] = $record->unpayedoverhours;
			
			$record_decoded["recordduration"] = $record->recordduration;
			$record_decoded["description"] = $record->description;		

			$recordlist_decoded[] = $record_decoded;
											
		}
		
		// return
		return $recordlist_decoded;
		
		
	}

// ==================================================================================================================
	// Constructing this instance
     public function __construct(string $AppName, IRequest $request, $userId,
	 							 TimesheetService $service){
         parent::__construct($AppName, $request);
		 
		 // initialize variables
		 $this->request = $request;
		 $this->userId = $userId;
		 $this->service = $service;
     }
	 

// ==================================================================================================================	
     /**
      * @NoAdminRequired
      * 
	  * @param string $title
	  * @param string $content	  
      */
     public function create() {

		// validation of record data
		$valid_data = $this->validate_recorddate(); 
		
		if ( strpos($valid_data, "ERROR") == true) {
			// Error -> send it back to form
			return new DataResponse( $valid_data );
			
		} else {
			
			// Use service to save the record data in a database
			$serviceResponse = $this->service->create($valid_data, $this->userId);
			return new DataResponse($serviceResponse);
		 
		}
		
     }

// ==================================================================================================================	
     /**
      * @NoAdminRequired
      * 
	  * @param string $title
	  * @param string $content	  
      */
     public function update(int $id) {

		// validation of record data
		$valid_data = $this->validate_recorddate(); 
		
		if ( strpos($valid_data, "ERROR") == true) {
			
			// Error -> send it back to form
			return new DataResponse( $valid_data );
			
		} else {
			
				
			// Use service to save the record data in a database
			$serviceResponse = $this->service->update($id, $valid_data, $this->userId);
			return new DataResponse($serviceResponse);
			
			
		 
		}
     }

// ==================================================================================================================	
     /**
      * @NoAdminRequired
      * 
	  * @param int $id  
      */
     public function delete(int $id) {	
	 	// New Error Handler function, which calls the find function
		 return $this->handleNotFound(function () use ($id) {
		 // now find the id and show it			 
            return $this->service->delete($id, $this->userId);
        });
     }

// ==================================================================================================================	
     /**
      * @NoAdminRequired
      * 
	  * @param int $id  
      */
     public function show(int $id) {	
	 	// New Error Handler function, which calls the find function
		 return $this->handleNotFound(function () use ($id) {
		 // now find the id and show it			 
            return $this->service->find($id, $this->userId);
        });
     }

// ==================================================================================================================	
     /**
      * @NoAdminRequired
      * 
      */
     public function showAll() {	
		 // now find the all from userid and show it
		 $recordlist = $this->service->findAll($this->userId);
		 
		 // Sort content by date (highest ID first)
		 usort($recordlist, function($a, $b) {
		 	return $a->startdatetime > $b->startdatetime ? -1 : 1; //Compare the id
		 });   
		 
		 // read records and cast into format for jquery
		$recordlist_decoded = $this->read_recorddates($recordlist);
		 
		 // Return
		 return $recordlist_decoded;
     }	 

 }