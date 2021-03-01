<?php
namespace OCA\Timesheet\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class WorkReport extends Entity implements JsonSerializable {

	// protected $id <-- already defined in Entity class
	public $monyearid;	
	public $userId;
	
	public $regularweeklyhours;
    public $regulardays;
		
	public $actualhours;
	public $targethours;
	public $overtime;
	public $overtimeunpayed;
	public $vacationdays;
	
	public $recalc;
		

			
    public function jsonSerialize() {
        return [
				
			// Record Entitiy ID
            'id' => $this->id,
			'userId' => $this->userId,
			'monyearid' => $this->monyearid,
								
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
			'recalc' => $this->recalc
						
        ];
    }
}