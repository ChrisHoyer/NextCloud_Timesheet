<?php
namespace OCA\Timesheet\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class Record extends Entity implements JsonSerializable {

	// protected $id <-- already defined in Entity class
    public $startdatetime;
    public $enddatetime;
	public $breaktime;
	
	public $recordduration;
	public $description;
	public $timezoneoffset;
	public $userId;

	public $tags;
	public $projects;
			
    public function jsonSerialize() {
        return [
				
			// Record Entitiy ID
            'id' => $this->id,
			
			// Start/End Time and Date
            'startdatetime' => $this->startdatetime,
            'enddatetime' => $this->enddatetime,
									
			// Breaktime and record time
            'breaktime' => $this->breaktime,
			'recordduration' => $this->recordduration,
			
			// Additional stuff like description, tags and timezone
            'description' => $this->description,
			'tags' => $this->tags,
			'projects' => $this->projects,
            'timezoneoffset' => $this->timezoneoffset,
			'userId' => $this->userId
        ];
    }
}