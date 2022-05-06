<?php

  namespace OCA\Timesheet\Migration;

  use Closure;
  use OCP\DB\Types;
  use OCP\DB\ISchemaWrapper;
  use OCP\IDBConnection;
  use OCP\Migration\SimpleMigrationStep;
  use OCP\Migration\IOutput;

  class Version000704Date202205061032 extends SimpleMigrationStep {

	  	/** @var IDBConnection */
		private $connection;

		/**
		 * Version000704Date202205061032 constructor.
		 *
		 * @param IDBConnection $connection
		 */
		public function __construct(IDBConnection $connection) {
			$this->connection = $connection;
		}
	  
      /**
        * @param IOutput $output
        * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
        * @param array $options
        * @return null|ISchemaWrapper
       */
       public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
          /** @var ISchemaWrapper $schema */
          $schema = $schemaClosure();

		  // Table "timesheet_projects"
          if (!$schema->hasTable('timesheet_projects')) {
			  
			  // create Table
			  $table = $schema->createTable('timesheet_projects');
			         
			  // Project ID  and user ID
              $table->addColumn('id', 'integer', [
                  'autoincrement' => true,
                  'notnull' => true,
              ]);
			  
              $table->addColumn('user_id', 'string', [
				  'notnull' => true,
                  'length' => 200,
				  'default' => '',
              ]);			  
			  
			  // Project Name
              $table->addColumn('projectname', 'text', [
                  'notnull' => true,
              ]);
			  
			  // Project Description
              $table->addColumn('description', 'text', [
                  'notnull' => true,
                  'default' => '',
              ]);		
			  
			  // Parent Project
              $table->addColumn('parentid', 'integer', [
                  'default' => 0,
              ]);	
			  
			  // Expected Duration
			  $table->addColumn('plannedduration', 'float', [
				  'default' => 0,
                  'notnull' => true,
              ]);
			  
			  // Key
              $table->setPrimaryKey(['id']);
			  		  
			  
          }
		   
		  // Modify "timesheet_reports"
          if ($schema->hasTable('timesheet_records')) {
			  
			  // Get Table
			  $table = $schema->getTable('timesheet_records');
			  
			  // Drop Projects Columnname
			  if ($table->hasColumn('projects')) {
				  $table->dropColumn('projects');
			  }
			  
			  // Add Assigned Project
			  if (!$table->hasColumn('assignedproject')) {			  
				  $table->addColumn('assignedproject', 'integer', [
					  'default' => 0,
					  'notnull' => true,
				  ]);
			  }
          }
		   		   

          return $schema;
      }
  
  
	 /**
	   * @param IOutput $output
	   * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	   * @param array $options
	 */
	public function postSchemaChange(IOutput $output, Closure $schemaClosure, array $options) {

		// Not Needed. ID starts with 1
		/* // Create Insert Query
		$insert = $this->connection->getQueryBuilder();
		$insert->insert('timesheet_projects')
			->values([
				'name' => $insert->createParameter('project_name'),
				'description' => $insert->createParameter('project_description'),
			]);
		
		// check if there are existing entries
		$query = $this->connection->getQueryBuilder();
		$query->select('*')
			->from('timesheet_projects');
		
		$result = $query->executeQuery();
		
		// if no entry, insert
		if($result->rowCount() == 0)
		{
			$insert
				->setParameter('project_name', '- unassigned -')
				->setParameter('project_description', 'unassigned records');	
			
			$insert->executeStatement();
		}*/
			
	}
  
  }
