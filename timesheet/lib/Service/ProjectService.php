<?php
namespace OCA\Timesheet\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\Timesheet\Db\Project;
use OCA\Timesheet\Db\ProjectMapper;

class ProjectService {
	
	// database report mapper instance
	private $projectmapper;
	
// ==================================================================================================================	
	
	// Create Report Service class
     public function __construct(ProjectMapper $projectmapper){
		 
		 // initialize mapper
		 $this->projectmapper = $projectmapper;
	 }	

// ==================================================================================================================	
	// Exception Handler (private)
	private function handleException($e){
		
		// if not exist or multiple objects are returned
        if ($e instanceof DoesNotExistException || $e instanceof MultipleObjectsReturnedException) {
			
			// Object not found
            throw new NotFoundException($e->getMessage());
			
        } else {
            throw $e;
        }
	}
	
// ==================================================================================================================	
	// insert a record into the database
	public function create(Project $project, string $userId) {
		 
		 //insert in table
		 return $this->projectmapper->insert($project);
     }
	
// ==================================================================================================================	
	// Update an entry
	public function update(int $id, Project $new_project, string $userId) {
		
		 // Try to find and update the Id and User ID
		 try {
		 	
			// find ID
			$project = $this->projectmapper->find($id, $userId);
			
			// Check Name and Description
		 	$project->setName($new_project->name);
		 	$project->setDescription($new_project->description);
		
			// Parent ID
		 	$project->setParentid($new_project->parentid);	

			 // Planned Duration
			 $project->setPlannedDuration($new_project->plannedduration);	
						
		 	//insert in table
		 	return $this->projectmapper->update($record);	
		 		
		 // Id not found
		 } catch(Exception $e) {
			 
			 // Exception Handler
			 $this->handleException($e);
		 } 			 
     }

	// ==================================================================================================================	
 	 // Find all entry
	 public function findAllProjectnames(string $userId){
		 
		 // Try to find the Id and User ID
		 try {
		 	
			$response = $this->projectmapper->findAllProjectnames($userId);	
				  
		 // Id not found
		 } catch(Exception $e) {
			 
			 // Exception Handler
			 $this->handleException($e);
		 }
		 
		 // return type
		 $projectlist;
		 	
		 // Projects found?
		 if(!empty($response))
		 {
			 foreach ($response as &$project)
			 {				 
				 $projectlist[$project->id] = $project->projectname; 
			 }			 
		 }
		 
		 return $projectlist;
		 
	 }
	
// ==================================================================================================================	
 	 // Find all entry
	 public function findAll(string $userId){
		 
		 // Try to find the Id and User ID
		 try {
		 	
			return $this->projectmapper->findAll($userId);	
				  
		 // Id not found
		 } catch(Exception $e) {
			 
			 // Exception Handler
			 $this->handleException($e);
		 }
		 
	 }  
	
}