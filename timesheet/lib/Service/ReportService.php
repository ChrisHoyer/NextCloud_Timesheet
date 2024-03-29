<?php
namespace OCA\Timesheet\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\Timesheet\Db\Report;
use OCA\Timesheet\Db\ReportMapper;

class ReportService {
	
	// database report mapper instance
	private $reportmapper;
	
// ==================================================================================================================	
	// Create Report Service class
     public function __construct(ReportMapper $reportmapper){
		 
		 // initialize mapper
		 $this->reportmapper = $reportmapper;
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
	// insert a report into the database
	public function create(Report $report, string $userId) {
		 
		 //insert in table
		 return $this->reportmapper->insert($report);
		 
     }	

// ==================================================================================================================	
	// Update an entry
	public function update(int $dbid, Report $new_report, string $userId) {
		
		 // Try to find and update the Id and User ID
		 try {
		 	
			// find ID and then delete it
			$report = $this->reportmapper->find($dbid, $userId);
			
			// Copy all data to new old record
			$report->setRegularweeklyhours($new_report->regularweeklyhours);
			$report->setRegulardays($new_report->regulardays);

			 
			$report->setStartreport($new_report->startreport);
			$report->setEndreport($new_report->endreport);
			 
			$report->setActualhours($new_report->actualhours);
			$report->setTargethours($new_report->targethours);
						
			$report->setOvertime($new_report->overtime);
			$report->setOvertimeunpayed($new_report->overtimeunpayed);
			$report->setVacationdays($new_report->vacationdays);
			 
			 // set Flags
			$report->setSignedoff($new_report->signedoff);		 
							 
		 	//insert in table
		 	return $this->reportmapper->update($report);	
		 		
		 // Id not found
		 } catch(Exception $e) {
			 
			 // Exception Handler
			 $this->handleException($e);
		 } 			
     }

// ==================================================================================================================	
	// Get Overtime except for this ID
	public function GetOvertime(string $monyearid, string $userId) {

		 // Try to find and update the Id and User ID
		 try {
		 	
			// find existing reports except current one
			$overtime_list = $this->reportmapper->findAllOvertime($monyearid, $userId);	
			 
			// Variable with acculumated overtime
			$overtime = floatval(0.0);
			
			// Extract existing dates from records			
			foreach ($overtime_list as &$item) {
				
				$overtime = $overtime + floatval($item->overtime);
			}
			 
			return $overtime;
		 		
		 // Id not found
		 } catch(Exception $e) {
			 
			 // Exception Handler
			 $this->handleException($e);
		 } 			
 
     }
	
// ==================================================================================================================		 
	// Update an entry
	public function updateReport($recordsummary, string $userId) {
		
		 // Try to find and update the Id and User ID
		 try {
		 	
			// find ID and then delete it
			$report = $this->reportmapper->findMonYear($recordsummary["reportID"], $userId)[0];
						
			// Copy all data to new old record			
			$report->setActualhours($recordsummary["total_duration_hours"]);
			$report->setTargethours($recordsummary["target_duration_hours"]);
						
			$report->setOvertime($recordsummary["difference_duration_hours"]);
			$report->setVacationdays($recordsummary["vacationdays"]);

												
		 	//insert in table
		 	return $this->reportmapper->update($report);
			
			//clearRecalcReportFlag($recordsummary->reportID, $userId);	
		 		
		 // Id not found
		 } catch(Exception $e) {
			 
			 // Exception Handler
			 $this->handleException($e);
		 }
		return;
     }	 
	   	 
// ==================================================================================================================
 	 // Find all entry
	 public function findAll(string $userId){
		 
		 // Try to find the Id and User ID
		 try {
		 	
			return $this->reportmapper->findAll($userId);	
				  
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
		 	
			return $this->reportmapper->getLastEntry($userId);	
				  
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
		 	
			return $this->reportmapper->findMonYear($monyearid, $userId);	
				  
		 // Id not found
		 } catch(Exception $e) {
			 
			 // Exception Handler
			 $this->handleException($e);
		 } 
	 }
	 	 
	 
}