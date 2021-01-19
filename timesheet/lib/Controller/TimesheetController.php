<?php
 namespace OCA\Timesheet\Controller;

 use OCP\IRequest;
 use OCP\AppFramework\Controller;
 
 use OCP\AppFramework\Http;
 use OCP\AppFramework\Http\TemplateResponse;
 use OCP\AppFramework\Http\DataResponse;
 use OCP\AppFramework\Http\JSONResponse;
 
 use OCA\Timesheet\Service\TimesheetService;
 use OCA\Timesheet\Service\FrameworkService;
 
 use OCA\Timesheet\Db\WorkRecord;
 
 class TimesheetController extends Controller {

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
	 							 TimesheetService $service, FrameworkService $fwservice){
         parent::__construct($AppName, $request);
		 
		 // initialize variables
		 $this->request = $request;
		 $this->userId = $userId;
		 $this->service = $service;
		 $this->fwservice = $fwservice;
     }

// ==================================================================================================================	
	/**
	 * CAUTION: the @Stuff turns off security checks; for this page no admin is
	 *          required and no CSRF check. If you don't know what CSRF is, read
	 *          it up in the docs or you might create a security hole. This is
	 *          basically the only required method to add this exemption, don't
	 *          add it to any other method if you don't exactly know what it does
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function index() {
		return new TemplateResponse('timesheet', 'index',['appPage' => 'content/index', 'script' => 'timesheet', 'style' => 'timesheet']);  // templates/index.php
	}	 

// ==================================================================================================================	
     /**
      * @NoAdminRequired
      *  
      */
     public function create() {

		// validation of record data
		$valid_data = $this->fwservice->validate_RecordRequest($this->request, $this->userId); 

		
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
	  * @param int $id  	  
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
     public function showAll($year, $month, $start, $end, $format) {
		 		 
		 //	 Check if parameters for year and month are defined
		 if( !is_null($year) & !is_null($month) ) {
			 
			 // Generate timestemp for the first and last day of the month in UNIX time
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
		$recordlist = $this->service->findAllRange($firstday, $lastday, $this->userId);
		
		// get all days of this month (if current month, only days in past)
		if($lastday > time()) $lastday = time();
		for($i=$lastday; $i>$firstday; $i-=86400) {$daylist[] = date('Y-m-d', $i);}	 
		
		 // read records and cast into format for jquery
		$recordlist_table = $this->fwservice->map_record2date($recordlist, $daylist);
		
		// some infos about search request
		$recordlist_table["requested"] = "Search between " .$firstday . " and " . $lastday;
		$recordlist_table["request_arg"] = "year=" . $year . " month=" . $month . " start=" . $start . " end=" . $end . " format=" . $format;
				
		 // Return
		 return $recordlist_table;
		 
		
		 
		 
     }	 

 }