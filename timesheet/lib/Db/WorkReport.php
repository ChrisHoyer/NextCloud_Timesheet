<?php
namespace OCA\Timesheet\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class WorkReport extends Entity implements JsonSerializable {

	// protected $id <-- already defined in Entity class
    public $regularweeklyhours;
    public $regulardays;
	public $vacation;
		
	public $actualhours;
	public $targethours;
	public $overtimepayed;
	public $overtimeunpayed;
	public $overtimecompensation;
		
	public $monyearid;	
	public $userId;
			
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
            'overtimepayed' => $this->overtimepayed,
            'overtimeunpayed' => $this->overtimeunpayed,
            'overtimecompensation' => $this->overtimecompensation,
			'vacation' => $this->vacation
						
        ];
    }
}