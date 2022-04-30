<?php
namespace OCA\Timesheet\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class ReportMapper extends QBMapper {
	
// got functionality from parent: insert, delete and update function
// ==================================================================================================================	
	// maps a report from database into a report file format
	/**
	* this class deals with the direct database connection for the table "timehseet_reports".
	* the content contains a report, which means a summery of the monthy reported time and project
	*/
    public function __construct(IDbConnection $db) {
        parent::__construct($db, 'timesheet_reports', Report::class);
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
    public function findMonYear(string $monyearid, string $userId) {
		
		// Build SQL querry
        $qb = $this->db->getQueryBuilder();

		// select from DB, where only current user
        $qb->select('*')
           ->from($this->getTableName())
           ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
		   ->andWhere($qb->expr()->eq('monyearid', $qb->createNamedParameter($monyearid)))
		   ->setParameter('monyearid', $monyearid);
		   
        return $this->findEntities($qb);
    } 

// ==================================================================================================================
public function findAllOvertime(string $monyearid, string $userId) {
		
		// Build SQL querry
        $qb = $this->db->getQueryBuilder();

		// select from DB, where only current user
        $qb->select('overtime')
           ->from($this->getTableName())
           ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
		   ->andWhere($qb->expr()->neq('monyearid', $qb->createNamedParameter($monyearid)))
		   ->setParameter('monyearid', $monyearid);
		   
        return $this->findEntities($qb);
    } 

// ==================================================================================================================
  	/**
	 * Returns all reports by userId
	 * @param string $userId
	 * @return array with entities
	 */
	public function findAll(string $userId) {
		
		// Build SQL querry
        $qb = $this->db->getQueryBuilder();

		// select from DB, where only current user
        $qb->select('*')
           ->from($this->getTableName())
           ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)));

        return $this->findEntities($qb);
		
    }

// ==================================================================================================================
    public function getLastEntry(string $userId) {
		
		// Build SQL querry
        $qb = $this->db->getQueryBuilder();

		// select highest ID from DB, where only current user
        $qb->select('*')
           ->from($this->getTableName())
           ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
		   ->orderBy('id', 'DESC')
		   ->setMaxResults(1);
		   
        return $this->findEntities($qb);
		

    }
		
// ================================================================================================================== 
}