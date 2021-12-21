<?php

  namespace OCA\Timesheet\Migration;

  use Closure;
  use OCP\DB\ISchemaWrapper;
  use OCP\Migration\SimpleMigrationStep;
  use OCP\Migration\IOutput;

  class Version000662Date202110122123 extends SimpleMigrationStep {

      /**
        * @param IOutput $output
        * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
        * @param array $options
        * @return null|ISchemaWrapper
       */
       public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
          /** @var ISchemaWrapper $schema */
          $schema = $schemaClosure();

		  // Modify "Timesheet_records"
          if ($schema->hasTable('timesheet_records')) {
			  
			  // Get Table
			  $table = $schema->getTable('timesheet_records');
			  
			  // Drop Table, otherwise SQL lite has problems
			  if ($table->hasIndex('timesheet_user_id_index')) {
				  $table->dropIndex('timesheet_user_id_index');
			  }
			  
          }

		  // Modify "timesheet_reports"
          if ($schema->hasTable('timesheet_reports')) {
			  
			  // Get Table
			  $table = $schema->getTable('timesheet_records');
			  
			  // Drop Table, otherwise SQL lite has problems
			  if ($table->hasIndex('timesheet_user_id_index')) {
				  $table->dropIndex('timesheet_user_id_index');
			  }
			  
          }
		   

          return $schema;
      }
  }
