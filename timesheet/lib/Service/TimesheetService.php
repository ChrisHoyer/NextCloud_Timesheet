<?php
namespace OCA\Timesheet\Service;

use OCA\Timesheet\Db\WorkRecord;
use OCA\Timesheet\Db\WorkRecordMapper;

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
     public function __construct(WorkRecordMapper $mapper){
		 // initialize variables
		 $this->mapper = $mapper;
	 }

// ==================================================================================================================	
	// Create a new entry
	public function create(WorkRecord $record, string $userId) {
		 
		 //insert in table
		 return $this->mapper->insert($record);
		 
     }	

// ==================================================================================================================
	// read all dates from existing records
	public function read_existingdates(string $userId){
		
		// get values
		return $this->mapper->getDates($userId);
		
	}
	
// ==================================================================================================================	
	// Update an entry
	public function update(int $id, WorkRecord $new_record, string $userId) {
		
		 // Try to find and update the Id and User ID
		 try {
		 	
			// find ID and then delete it
			$record = $this->mapper->find($id, $userId);
			
			// Copy all data to new old record
			$record->setStartdatetime($new_record->startdatetime);
			$record->setEnddatetime($new_record->enddatetime);
			$record->setBreaktime($new_record->breaktime);
			
			$record->setRecordduration($new_record->recordduration);
			$record->setRegularhours($new_record->regularhours);
			$record->setUnpayedoverhours($new_record->unpayedoverhours);
						
			$record->setDescription($new_record->description);
			$record->setTimezoneoffset($new_record->timezoneoffset);
			
			$record->setHoliday($new_record->holiday);
			$record->setVacation($new_record->vacation);
						
						
		 	//insert in table
		 	return $this->mapper->update($record);	
		 		
		 // Id not found
		 } catch(Exception $e) {
			 
			 // Exception Handler
			 $this->handleException($e);
		 } 			
			

		 
     }	
	 
// ==================================================================================================================	
	 // delete an entry
	 public function delete(int $id, string $userId){
		 
		 // Try to find and delete the Id and User ID
		 try {
		 	
			// find ID and then delete it
			$record = $this->mapper->find($id, $userId);
			$this->mapper->delete($record);
			
			return $record;		  
		 // Id not found
		 } catch(Exception $e) {
			 
			 // Exception Handler
			 $this->handleException($e);
		 } 
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

// ==================================================================================================================
 	 // Find an entry for a specific month
	 public function findAllMonth(string $year, string $month, string $userId){
		 
		 // Generate timestemp for the first and last day of the month in UNIX time
		$firstday_month = strtotime(gmdate("Y-m-d", strtotime($year . "-" . $month . "-01")));
		$lastday_month = strtotime(gmdate("Y-m-t", strtotime($year . "-" . $month . "-01")));
		 
		 
		 // Try to find the Id and User ID
		 try {
		 	
			return $this->mapper->findAllMonth($firstday_month, $lastday_month, $userId);	
				  
		 // Id not found
		 } catch(Exception $e) {
			 
			 // Exception Handler
			 $this->handleException($e);
		 }
		 
	 } 
	   
};