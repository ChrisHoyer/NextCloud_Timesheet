<?php
namespace OCA\Timesheet\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class Record extends Entity implements JsonSerializable {

	// protected $id <-- already defined in Entity class
    protected $title;
    protected $content;
    protected $userId;

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content
        ];
    }
}