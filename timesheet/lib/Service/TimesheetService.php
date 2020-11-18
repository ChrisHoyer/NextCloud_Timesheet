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
	
	
	// Create Mapper while constructing this instance
     public function __construct(RecordMapper $mapper){
		 // initialize variables
		 $this->mapper = $mapper;
	 }
	
	// Create a new entry
	public function create(string $title, string $content, string $userId) {
		 
		 // create instance of database class
		 $record = new Record();
		 $record->setTitle($title);
		 $record->setContent($content);
		 $record->setUserId($userId);	
		 	 		 
		 //insert in table
		 return $this->mapper->insert($record);
     }	
	 
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
  
};