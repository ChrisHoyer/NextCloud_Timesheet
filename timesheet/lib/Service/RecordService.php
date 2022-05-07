<?php
namespace OCA\Timesheet\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\Timesheet\Db\Record;
use OCA\Timesheet\Db\RecordMapper;

/* Record Service Class
 *
 * This class deals with everything related to records. New records will be process and checked within this
 * class. 
 * 
 **/



class RecordService {
	
	// database record mapper instance
	private $recordmapper;
	
// ==================================================================================================================	
	// create record class instance
     public function __construct(RecordMapper $recordmapper){
		 
		 // initialize mapper
		 $this->recordmapper = $recordmapper;
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
	public function create(Record $record, string $userId) {
		 
		 //insert in table
		 return $this->recordmapper->insert($record);
		 
     }	
	
// ==================================================================================================================	
	// Update an entry
	public function update(int $id, Record $new_record, string $userId) {
		
		 // Try to find and update the Id and User ID
		 try {
		 	
			// find ID and then delete it
			$record = $this->recordmapper->find($id, $userId);
			
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
			 
			$record->setAssignedproject($new_record->assignedproject);
						
		 	//insert in table
		 	return $this->recordmapper->update($record);	
		 		
		 // Id not found
		 } catch(Exception $e) {
			 
			 // Exception Handler
			 $this->handleException($e);
		 } 			 
     }	
	 	
// ==================================================================================================================
	// read all dates from existing records
	public function read_existingdates(string $userId){
		
		// get values
		return $this->recordmapper->getDates($userId);
		
	}

// ==================================================================================================================	
	 // delete an entry
	 public function delete(int $id, string $userId){
		 
		 // Try to find and delete the Id and User ID
		 try {
		 	
			// find ID and then delete it
			$record = $this->recordmapper->find($id, $userId);
			$this->recordmapper->delete($record);
			
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
		 	
			return $this->recordmapper->find($id, $userId);		  
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
		 	
			return $this->recordmapper->findAll($userId);	
				  
		 // Id not found
		 } catch(Exception $e) {
			 
			 // Exception Handler
			 $this->handleException($e);
		 }
		 
	 } 

// ==================================================================================================================
 	 // Find an entry
	 public function findAllProjectTime(string $userId, int $projectid){
		 
		 // Try to find the Id and User ID
		 try {
		 	
			$response = $this->recordmapper->findAllProjectTime($userId, $projectid);	
				  
		 // Id not found
		 } catch(Exception $e) {
			 
			 // Exception Handler
			 $this->handleException($e);
		 }
		 
		 // Subtract End - Start Time for each Record and Accumulate it
		 $projecttime = 0;
		 	
		 // Projects found?
		 if(!empty($response))
		 {
			 foreach ($response as &$project)
			 {				 
				 $projecttime += intval(($project->enddatetime-$project->startdatetime)/60); 
			 }			 
		 }
		 
		 return $projecttime;
		 
	 } 

// ==================================================================================================================
 	 // Find an entry for a specific month
	 public function findAllRange(string $firstday, string $lastday, string $userId){
		 

		 // Try to find the Id and User ID
		 try {
		 	
			return $this->recordmapper->findAllStartDateRange($firstday, $lastday, $userId);	
				  
		 // Id not found
		 } catch(Exception $e) {
			 
			 // Exception Handler
			 $this->handleException($e);
		 }
		 
	 } 
	   
}