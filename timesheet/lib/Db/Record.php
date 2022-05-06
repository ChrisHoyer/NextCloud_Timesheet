<?php
namespace OCA\Timesheet\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class Record extends Entity implements JsonSerializable {

	// protected $id <-- already defined in Entity class
    public $startdatetime;
    public $enddatetime;
	public $breaktime;
	public $holiday;	
	public $vacation;
	public $unpayedoverhours;
		
	public $recordduration;
	public $regularhours;
	public $description;
	public $timezoneoffset;
	public $userId;

	public $tags;
	public $assignedproject;

			
    public function jsonSerialize() {
        return [
				
			// Record Entitiy ID
            'id' => $this->id,
			'userId' => $this->userId,
			
			// Start/End Time and Date
            'startdatetime' => $this->startdatetime,
            'enddatetime' => $this->enddatetime,
									
			// Breaktime and record time
            'breaktime' => $this->breaktime,
			'recordduration' => $this->recordduration,
			'regularhours' => $this->regularhours,
			'unpayedoverhours' => $this->unpayedoverhours,
						
			// User vacation and legal holidays
			'holiday' => $this->holiday,
			'vacation' => $this->vacation,			
			
			
			// Additional stuff like description, tags and timezone
            'description' => $this->description,
			'tags' => $this->tags,
			'assignedproject' => $this->assignedproject,
            'timezoneoffset' => $this->timezoneoffset

        ];
    }
}