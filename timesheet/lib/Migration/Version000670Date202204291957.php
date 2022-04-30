<?php

  namespace OCA\Timesheet\Migration;

  use Closure;
  use OCP\DB\ISchemaWrapper;
  use OCP\Migration\SimpleMigrationStep;
  use OCP\Migration\IOutput;

  class Version000670Date202204291957 extends SimpleMigrationStep {

      /**
        * @param IOutput $output
        * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
        * @param array $options
        * @return null|ISchemaWrapper
       */
       public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
          /** @var ISchemaWrapper $schema */
          $schema = $schemaClosure();


		  // Modify "timesheet_reports"
          if ($schema->hasTable('timesheet_reports')) {
			  
			  // Get Table
			  $table = $schema->getTable('timesheet_reports');
			  
			  // Drop Recalc Columnname
			  if ($table->hasColumn('recalc')) {
				  $table->dropColumn('recalc');
			  }
			  
			  // Add "Report signed off" Flag
			  $table->addColumn('signedoff', 'integer', []);

			  // Drop Overtimeacc Column
			  if ($table->hasColumn('overtimeacc')) {
				  $table->dropColumn('overtimeacc');
			  }
          }
		   

          return $schema;
      }
  }
