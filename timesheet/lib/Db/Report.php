<?php
namespace OCA\Timesheet\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class Report extends Entity implements JsonSerializable {

	// protected $id <-- already defined in Entity class
	public $userId;
	public $monyearid;
	
	public $startreport;
	public $endreport;
	
	public $regularweeklyhours;
    public $regulardays;
		
	public $actualhours;
	public $targethours;
	public $overtime;	
	public $overtimeunpayed;
	public $vacationdays;
	
	public $signedoff;
		

			
    public function jsonSerialize() {
        return [
				
			// Record Entitiy ID
            'id' => $this->id,
			'userId' => $this->userId,
			'monyearid' => $this->monyearid,

			// If the report should start within a month
			'startreport' => $this->startreport,
			'endreport' => $this->endreport,
			
			// Weekly Days and hours
            'regularweeklyhours' => $this->regularweeklyhours,
            'regulardays' => $this->regulardays,
			
			// Calculated Hours
            'actualhours' => $this->actualhours,
			'targethours' => $this->targethours,
            'overtime' => $this->overtime,
            'overtimeunpayed' => $this->overtimeunpayed,
			'vacationdays' => $this->vacationdays,
			
			// Flags
			'signedoff' => $this->signedoff
						
        ];
    }
}