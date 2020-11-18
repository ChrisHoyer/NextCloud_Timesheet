<?php
 namespace OCA\Timesheet\Controller;

 use OCP\IRequest;
 use OCP\AppFramework\Controller;

 use OCP\AppFramework\Http;
 use OCP\AppFramework\Http\DataResponse;
 use OCP\AppFramework\Http\JSONResponse;
 
 use OCA\Timesheet\Service\TimesheetService;
 use OCA\Timesheet\Db\Record;
 
 class TimesheetController extends Controller {

     // Private variables, which are necessary.
     private $userId;
	 private $service;
	 
	 protected $request;
	 private $request_calc;
	 
	 // use Errors.php
	 use Errors;

// ==================================================================================================================	
	// Check record data from request
	private function validate_recorddate(){

		 // Check user input: starttime/startdate before endtime/enddate
		 if (isset($this->request->starttime) & isset($this->request->startdate) & isset($this->request->endtime) & isset($this->request->enddate) ) {
			 
			 	// Get complete Start and End time for calculation of Duration
			 	$t_start = \DateTime::createFromFormat ("H:i Y-m-d", ($this->request->starttime . " " . $this->request->startdate));
				$t_end = \DateTime::createFromFormat ("H:i Y-m-d", ($this->request->endtime . " " . $this->request->enddate));
				
				// Calculate complete Duration in seconds
			 	$t_completeduration = $t_end->getTimestamp() - $t_start->getTimestamp();
				
				// Todo: If Negative, Error Message
				if ($t_completeduration < 0)
				{
					return false;
				}
				
				// Include Breaktime, if available (otherwise UNIX std time) 
				$t_break = \DateTime::createFromFormat ("H:i Y-m-d", ("00:00 1970-01-01"));
				if(isset($this->request->breaktime)){
					$t_break = \DateTime::createFromFormat ("H:i Y-m-d", ($this->request->breaktime . " 1970-01-01"));
				}
				
				// Calculate Working duration
			 	$t_workingduration = $t_completeduration - $t_break->getTimestamp();
				
				// Todo: If Negative, Error Message								
				if ($t_workingduration < 0)
				{
					return false;
				}
		 }
		 
		 // Save Working Duration as String (HH:MM)
		 $this->request_calc->recordduration = gmdate("H:i", $t_workingduration);
		 
		 // return ok
		 return true;
		 		
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
		if ($this->validate_recorddate() == false) {
			return new DataResponse("Error: Invalid Data.");
		}
		
		 // create instance of database class
		 $record = new Record();
		 $record->setUserId($this->userId);
		 
		 $record->setStartdate($this->request->startdate);
		 $record->setStarttime($this->request->starttime);
		 $record->setEnddate($this->request->enddate);
		 $record->setEndtime($this->request->endtime);
		 
		 $record->setBreaktime($this->request->breaktime);
		 $record->setRecordduration($this->request_calc->recordduration);
		 
		 $record->setDescription($this->request->description);
		 $record->setTimezoneoffset($this->request->timezoneoffset);
		 
		 		 								
		 // Use service to save the record data in a database
		 //$serviceResponse = $this->service->create($record, $this->userId);
		 //return new DataResponse($serviceResponse);
		 return new DataResponse($record);
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
            return $this->service->findAll($this->userId);
     }	 

 }