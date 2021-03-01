<?php
namespace OCA\Timesheet\Service;

use OCA\Timesheet\Db\WorkReport;
use OCA\Timesheet\Db\WorkReportMapper;

use Exception;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

class ReportService {
	
	// Database Mapper
	private $WRmapper;
	
	// ==================================================================================================================	
	// Create Mapper while constructing this instance
     public function __construct(WorkReportMapper $WRmapper){
		 // initialize variables
		 $this->WRmapper = $WRmapper;
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
	// Create a new entry
	public function create(WorkReport $report, string $userId) {
		 
		 //insert in table
		 return $this->WRmapper->insert($report);
		 
     }	

// ==================================================================================================================	
	// Update an entry
	public function update(int $dbid, WorkReport $new_report, string $userId) {
		
		 // Try to find and update the Id and User ID
		 try {
		 	
			// find ID and then delete it
			$report = $this->WRmapper->find($dbid, $userId);
			
			// Copy all data to new old record
			$report->setRegularweeklyhours($new_report->regularweeklyhours);
			$report->setRegulardays($new_report->regulardays);
			
			$report->setActualhours($new_report->actualhours);
			$report->setTargethours($new_report->targethours);
						
			$report->setOvertime($new_report->overtime);
			$report->setOvertimeunpayed($new_report->overtimeunpayed);
			$report->setVacationdays($new_report->vacationdays);
			
		
		 	//insert in table
		 	return $this->WRmapper->update($report);	
		 		
		 // Id not found
		 } catch(Exception $e) {
			 
			 // Exception Handler
			 $this->handleException($e);
		 } 			
 
     }
	 
// ==================================================================================================================		 
	// Update an entry
	public function Recordlist2Report($recordsummary, string $userId) {
		
		 // Try to find and update the Id and User ID
		 try {
		 	
			// find ID and then delete it
			$report = $this->WRmapper->findMonYear($recordsummary["reportID"], $userId)[0];
						
			// Copy all data to new old record			
			$report->setActualhours($recordsummary["total_duration_hours"]);
			$report->setTargethours($recordsummary["target_duration_hours"]);
						
			$report->setOvertime($recordsummary["difference_duration_hours"]);
			$report->setVacationdays($recordsummary["vacationdays"]);

			//clear flag
			$report->setRecalc(0);
												
		 	//insert in table
		 	return $this->WRmapper->update($report);
			
			clearRecalcReportFlag($recordsummary->reportID, $userId);	
		 		
		 // Id not found
		 } catch(Exception $e) {
			 
			 // Exception Handler
			 $this->handleException($e);
		 }
		return;
     }	 
	 
	 
	 // ==================================================================================================================
	// tidy up record data from return
	public function setRecalcReportFlag(string $monyearid, string $userId){
			
		 // Try to find the Id and User ID
		 try {
		 	
			$report = $this->WRmapper->findMonYear($monyearid, $userId)[0];
			
			// only set flag
			$report->setRecalc(1);

		 	//insert in table
		 	return $this->WRmapper->update($report);			
				  
		 // Id not found
		 } catch(Exception $e) {
			 
			 // Exception Handler
			 $this->handleException($e);
		 } 
	 }
 	 
	// ==================================================================================================================
 	 // Find all entry
	 public function findAll(string $userId){
		 
		 // Try to find the Id and User ID
		 try {
		 	
			return $this->WRmapper->findAll($userId);	
				  
		 // Id not found
		 } catch(Exception $e) {
			 
			 // Exception Handler
			 $this->handleException($e);
		 }
		 
	 }  

	// ==================================================================================================================
 	 // Find all entry
	 public function getLastEntry(string $userId){
		 
		 // Try to find the Id and User ID
		 try {
		 	
			return $this->WRmapper->getLastEntry($userId);	
				  
		 // Id not found
		 } catch(Exception $e) {
			 
			 // Exception Handler
			 $this->handleException($e);
		 }
		 
	 }
	 
// ==================================================================================================================	 
	 // Find an entry
	 public function findMonYear(string $monyearid, string $userId){
		 
		 // Try to find the Id and User ID
		 try {
		 	
			return $this->WRmapper->findMonYear($monyearid, $userId);	
				  
		 // Id not found
		 } catch(Exception $e) {
			 
			 // Exception Handler
			 $this->handleException($e);
		 } 
	 }
	 	 
	 
}