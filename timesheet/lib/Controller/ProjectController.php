<?php
 namespace OCA\Timesheet\Controller;

 use OCP\IRequest;
 use OCP\AppFramework\Controller;
 
 use OCP\AppFramework\Http;
 use OCP\AppFramework\Http\TemplateResponse;
 use OCP\AppFramework\Http\DataResponse;
 use OCP\AppFramework\Http\JSONResponse;
 
 use OCA\Timesheet\Service\FrameworkService;
 use OCA\Timesheet\Service\ProjectService;
 
 class ProjectController extends Controller {

     // Private variables, which are necessary.
     private $userId;
	 private $service;
	 private $fwservice;
	 
	 protected $request;

// ==================================================================================================================
	// Constructing this instance
     public function __construct(string $AppName, IRequest $request, $userId, ProjectService $service, FrameworkService $fwservice){
         parent::__construct($AppName, $request);
		 
		 // initialize variables
		 $this->request = $request;
		 $this->userId = $userId;
		 $this->service = $service;
		 $this->fwservice = $fwservice;
     }

	 // ==================================================================================================================	
     /**
      * This function is called, if a new record data request comes in. Either a new record should be created or an existing
	  * record will be updated. This depends on the the existing ID of this record. An empty ID triggers a record creation
	  *
	  * @NoAdminRequired
      */
     public function createupdateProject() {
		 
		// validation of record data
		$valid_data = $this->fwservice->validate_ProjectReq($this->request, $this->userId); 
			 
		if ( strpos($valid_data, "ERROR") == true) {
			// Error -> send it back to form
			return new DataResponse( $valid_data );
		}
			
		// check if database entry exists 
		$requestID = $this->request->id;
				
		if (!empty($requestID) ) {
			$existingID = $this->service->find($requestID, $this->userId);
		} else {
			$existingID = "";
		}
		
		// create new ID, if nothing found
		if (empty($existingID)){	
			$serviceResponse = $this->service->create($valid_data, $this->userId);
			
		// ID found	
		} else {
			// Use service to save the record data in a database			
			$serviceResponse = $this->service->update($requestID, $valid_data, $this->userId);
		}
							

		return new DataResponse($serviceResponse);
	}	
     
// ==================================================================================================================	
     /**
      * @NoAdminRequired
      * 
      */
     public function getProjectlist() {
		 		 
		 // now find all projects of this user		 
		 $response = $this->service->findAllProjectnames($this->userId);
		 	
		 // Return
		 return $response;
     }	 
// ==================================================================================================================	
     /**
      * @NoAdminRequired
      * 
      */
     public function getProjects() {
		 
		 // now find the id and show it			 
		 $reportlist = $this->service->findAll($this->userId);
		 	 		 	 
		 // Return
		 return $reportlist;
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
		return new TemplateResponse('timesheet', 'index',['appPage' => 'content/project', 'script' => 'project', 'style' => 'project']);  // templates/index.php
	}	 
	
 }