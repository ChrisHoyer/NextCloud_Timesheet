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
			
			$report->setVacation($new_report->vacation);
			$report->setActualhours($new_report->actualhours);
			$report->setTargethours($new_report->targethours);
						
			$report->setOvertimepayed($new_report->overtimepayed);
			$report->setOvertimeunpayed($new_report->overtimeunpayed);
			$report->setOvertimecompensation($new_report->overtimecompensation);
						
						
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