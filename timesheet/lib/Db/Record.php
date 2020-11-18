<?php
namespace OCA\Timesheet\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class Record extends Entity implements JsonSerializable {

	// protected $id <-- already defined in Entity class
    protected $startdate;
    protected $starttime;
    protected $enddate;
    protected $endtime;
	protected $breaktime;
	protected $recordduration;
	protected $description;
	protected $timezoneoffset;
	protected $userId;
	
	protected $title;
    protected $content;
		
    public function jsonSerialize() {
        return [
		
			// old stuff
            'title' => $this->title,
            'content' => $this->content,
				
			// Record Entitiy ID
            'id' => $this->id,
			// Start/End Time and Date
            'startdate' => $this->startdate,
            'starttime' => $this->starttime,
            'enddate' => $this->enddate,
            'endtime' => $this->endtime,
			// Breaktime and record time
            'breaktime' => $this->breaktime,
			'recordduration' => $this->recordduration,
			// Additional stuff like description and timezone
            'description' => $this->description,
            'timezoneoffset' => $this->timezoneoffset,
			'userId' => $this->userId
        ];
    }
}