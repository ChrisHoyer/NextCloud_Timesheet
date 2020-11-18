<?php
namespace OCA\Timesheet\Service;

use OCA\Timesheet\Db\Record;
use OCA\Timesheet\Db\RecordMapper;

use Exception;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

class TimesheetService {
	
	// Database Mapper
	private $mapper;

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
	// Create Mapper while constructing this instance
     public function __construct(RecordMapper $mapper){
		 // initialize variables
		 $this->mapper = $mapper;
	 }

// ==================================================================================================================	
	// Create a new entry
	public function create(string $title, string $content, string $userId) {
		 
		 //insert in table
		 return $this->mapper->insert($record);
     }	

// ==================================================================================================================	 
	 // Find an entry
	 public function find(int $id, string $userId){
		 
		 // Try to find the Id and User ID
		 try {
		 	
			return $this->mapper->find($id, $userId);		  
		 // Id not found
		 } catch(Exception $e) {
			 
			 // Exception Handler
			 $this->handleException($e);
		 } 
	 }
  
// ==================================================================================================================
 	 // Find an entry
	 public function findAll(string $userId){
		 
		 // Try to find the Id and User ID
		 try {
		 	
			return $this->mapper->findAll($userId);	
				  
		 // Id not found
		 } catch(Exception $e) {
			 
			 // Exception Handler
			 $this->handleException($e);
		 } 
	 } 
  
};