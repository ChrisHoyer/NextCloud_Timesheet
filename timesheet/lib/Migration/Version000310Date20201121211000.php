<?php

  namespace OCA\Timesheet\Migration;

  use Closure;
  use OCP\DB\ISchemaWrapper;
  use OCP\Migration\SimpleMigrationStep;
  use OCP\Migration\IOutput;

  class Version000310Date20201121211000 extends SimpleMigrationStep {

      /**
        * @param IOutput $output
        * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
        * @param array $options
        * @return null|ISchemaWrapper
       */
       public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
          /** @var ISchemaWrapper $schema */
          $schema = $schemaClosure();

		  // generate new Table with record content
          if (!$schema->hasTable('timesheet_records')) {
			  
			  // create Table
			  $table = $schema->createTable('timesheet_records');
			         
			  // Table Layout ID  and user ID
              $table->addColumn('id', 'integer', [
                  'autoincrement' => true,
                  'notnull' => true,
              ]);
              $table->addColumn('user_id', 'string', [
                  'notnull' => true,
                  'length' => 200,
              ]);			  
			  
			  // Table Layout Start End (UNIX STD Time)
              $table->addColumn('startdatetime', 'integer', [
                  'notnull' => true,
              ]);
              $table->addColumn('enddatetime', 'integer', [
                  'notnull' => true,
              ]);
			  		  
			  // and Break (UNIX STD Time) and Record Duration time
			  $table->addColumn('breaktime', 'integer', [
                  'notnull' => true,
              ]);
              $table->addColumn('recordduration', 'string', [
                  'notnull' => true,
              ]);
			  		  
		  			  
			  // Table Description, Tags, projects and Timezone
              $table->addColumn('description', 'text', [
                  'notnull' => true,
                  'default' => '',
              ]);
			  $table->addColumn('tags', 'text', [
                  'notnull' => true,
                  'default' => '',
              ]);
			  $table->addColumn('projects', 'text', [
                  'notnull' => true,
                  'default' => '',
              ]);
              $table->addColumn('timezoneoffset', 'integer', [
			      'notnull' => true,
              ]);			  
			  
			  // Keys
              $table->setPrimaryKey(['id']);
              $table->addIndex(['user_id'], 'timesheet_user_id_index');
          }
		  	  

          return $schema;
      }
  }
