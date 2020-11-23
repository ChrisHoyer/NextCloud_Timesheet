<?php
namespace OCA\Timesheet\Service;

use OCA\Timesheet\Db\WorkRecord;
use OCA\Timesheet\Db\WorkRecordMapper;

use Exception;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

class ReportService {
	
	// Database Mapper
	private $WRmapper;
	
	// ==================================================================================================================	
	// Create Mapper while constructing this instance
     public function __construct(WorkRecordMapper $WRmapper){
		 // initialize variables
		 $this->WRmapper = $WRmapper;
	 }
	 
}