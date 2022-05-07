<?php
namespace OCA\Timesheet\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class RecordMapper extends QBMapper {
	
// got functionality from parent: insert, delete and update function
// ==================================================================================================================	
	// maps a record from database into a record file for
	/**
	* this class deals with the direct database connection for each individual record entity
	* It contains start/end time as well as a project and other usefull information to track working time.
	*/
    public function __construct(IDbConnection $db) {
        parent::__construct($db, 'timesheet_records', Record::class);
    }

// ==================================================================================================================
	// find using SQL commands
    public function find(int $id, string $userId) {
		
		// Build SQL querry
        $qb = $this->db->getQueryBuilder();
		
		// select from app Table, where only this ID and current user
		$qb->select('*')
		   ->from($this->getTableName())
		   ->where($qb->expr()->eq('id', $qb->createNamedParameter($id)))
		   ->andWhere($qb->expr()->eq('user_id', $qb->createNamedParameter($userId))
           );

        return $this->findEntity($qb);
    }

	// ==================================================================================================================
	// find using SQL commands
    public function findAllProjectTime(string $userId, int $projectid) {
		
		// Build SQL querry
        $qb = $this->db->getQueryBuilder();
		
		// select from app Table, where only this ID and current user
		$qb->select('startdatetime', 'enddatetime')
		   ->from($this->getTableName())
		   ->where($qb->expr()->eq('assignedproject', $qb->createNamedParameter($projectid)))
		   ->andWhere($qb->expr()->eq('user_id', $qb->createNamedParameter($userId))
           );

        return $this->findEntities($qb);
    }
	
// ==================================================================================================================
    public function findAll(string $userId) {
		
		// Build SQL querry
        $qb = $this->db->getQueryBuilder();

		// select from DB, where only current user
        $qb->select('*')
           ->from($this->getTableName())
           ->where(
            $qb->expr()->eq('user_id', $qb->createNamedParameter($userId))
           );
		
        return $this->findEntities($qb);
    } 

// ==================================================================================================================
    public function findAllStartDateRange(string $firstday, string $lastday, string $userId) {
		
		// Build SQL querry
        $qb = $this->db->getQueryBuilder();

		// select from DB, where only current user
        $qb->select('*')
           ->from($this->getTableName())
           ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
		   ->andWhere('startdatetime >= :firstday')
		   ->andWhere('startdatetime <= :lastday')
		   ->setParameter('firstday', $firstday)
		   ->setParameter('lastday', $lastday);
		   
        return $this->findEntities($qb);
    } 
	
// ==================================================================================================================
    public function getDates(string $userId) {
		
		// Build SQL querry
        $qb = $this->db->getQueryBuilder();

		// select from DB, where only current user
        $qb->select('startdatetime')
           ->from($this->getTableName())
           ->where(
            $qb->expr()->eq('user_id', $qb->createNamedParameter($userId))
           );

        return $this->findEntities($qb);
    } 
	
// ==================================================================================================================
}