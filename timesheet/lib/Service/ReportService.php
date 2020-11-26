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
 	 // Find an entry
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
}