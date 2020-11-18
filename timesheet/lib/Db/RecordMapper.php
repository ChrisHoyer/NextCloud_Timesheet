<?php
namespace OCA\Timesheet\Db;

use OCP\IDbConnection;
use OCP\AppFramework\Db\QBMapper;

class RecordMapper extends QBMapper {

    public function __construct(IDbConnection $db) {
        parent::__construct($db, 'timesheet_records', Record::class);
    }

	// got from parent: insert and update function

	// find using SQL commands
    public function find(int $id, string $userId) {
		
		// Build SQL querry
        $qb = $this->db->getQueryBuilder();

					// select from app Table, where only this ID and current user
                    $qb->select('*')
                             ->from($this->getTableName())
                             ->where(
                                     $qb->expr()->eq('id', $qb->createNamedParameter($id))
                             )->andWhere(
             $qb->expr()->eq('user_id', $qb->createNamedParameter($userId))
           );

        return $this->findEntity($qb);
    }

/*    public function findAll(string $userId) {
        $qb = $this->db->getQueryBuilder();

        $qb->select('*')
           ->from($this->getTableName())
           ->where(
            $qb->expr()->eq('user_id', $qb->createNamedParameter($userId))
           );

        return $this->findEntities($qb);
    } */

}