<?php
namespace OCA\Timesheet\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class ProjectMapper extends QBMapper {
	
// got functionality from parent: insert, delete and update function
// ==================================================================================================================	
	// maps a Project from database into a Project file for
	/**
	* this class deals with the direct database connection for each individual Project entity
	*/
    public function __construct(IDbConnection $db) {
        parent::__construct($db, 'timesheet_projects', Project::class);
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
    public function findAllProjectnames(string $userId) {
		
		// Build SQL querry
        $qb = $this->db->getQueryBuilder();

		// select from DB, where only current user
        $qb->select('projectname', 'id')
           ->from($this->getTableName())
           ->where(
            $qb->expr()->eq('user_id', $qb->createNamedParameter($userId))
           );
			
        return $this->findEntities($qb);
    } 
}