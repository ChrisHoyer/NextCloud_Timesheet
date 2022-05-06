<?php
namespace OCA\Timesheet\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class Project extends Entity implements JsonSerializable {

	// protected $id <-- already defined in Entity class
	public $userId;
	
    public $projectname;
	public $description;
	
	public $parentid;
	public $plannedduration;
	
	
    public function jsonSerialize() {
        return [
				
			// Record Entitiy ID
            'id' => $this->id,
			'userId' => $this->userId,
			
			// Name and Description
            'projectname' => $this->projectname,
            'description' => $this->description,
									
			// Parent Project and Planned Duration
            'parentid' => $this->parentid,
			'plannedduration' => $this->plannedduration,

        ];
    }
}