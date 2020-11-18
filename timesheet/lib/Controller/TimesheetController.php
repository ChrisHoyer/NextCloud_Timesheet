<?php
 namespace OCA\Timesheet\Controller;

 use OCP\IRequest;
 use OCP\AppFramework\Controller;

 use OCP\AppFramework\Http;
 use OCP\AppFramework\Http\DataResponse;
 
 use OCA\Timesheet\Service\TimesheetService;
 
 class TimesheetController extends Controller {

     // Private variables, which are necessary.
     private $userId;
	 private $service;
	 
	 // use Errors.php
	 use Errors;

	// Get UserID while constructing this instance
     public function __construct(string $AppName, IRequest $request, $userId,
	 							 TimesheetService $service){
         parent::__construct($AppName, $request);
		 // initialize variables
		 $this->userId = $userId;
		 $this->service = $service;
     }

     /**
      * @NoAdminRequired
      * 
	  * @param string $title
	  * @param string $content	  
      */
     public function create(string $title, string $content) {	
		 // Use service to save the data in a database
		 $serviceResponse = $this->service->create($title, $content, $this->userId);
		 return new DataResponse($serviceResponse);
     }

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
	 

 }