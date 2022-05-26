<?php

  namespace OCA\Timesheet\Migration;

  use Closure;
  use OCP\DB\ISchemaWrapper;
  use OCP\Migration\SimpleMigrationStep;
  use OCP\Migration\IOutput;

  class Version000420Date20201122191000 extends SimpleMigrationStep {

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
			  $table_records = $schema->createTable('timesheet_records');
			         
			  // Table Layout ID  and user ID
              $table_records->addColumn('id', 'integer', [
                  'autoincrement' => true,
                  'notnull' => true,
              ]);
              $table_records->addColumn('user_id', 'string', [
                  'notnull' => true,
                  'length' => 200,
              ]);			  
			  
			  // Table Layout Start End (UNIX STD Time)
              $table_records->addColumn('startdatetime', 'integer', [
                  'notnull' => true,
              ]);
              $table_records->addColumn('enddatetime', 'integer', [
                  'notnull' => true,
              ]);
			  		  
			  // and Break (UNIX STD Time) and Record Duration and regular hours time
			  $table_records->addColumn('breaktime', 'integer', [
                  'notnull' => true,
              ]);
              $table_records->addColumn('recordduration', 'string', [
                  'notnull' => true,
              ]);
              $table_records->addColumn('regularhours', 'string', [
                  'notnull' => true,
              ]);
			  $table_records->addColumn('unpayedoverhours', 'string', [
			      'notnull' => true,
                  'default' => 0,
              ]);
			  			  			  	
			  // legal holidays and vacation
			  $table_records->addColumn('holiday', 'string', [
                  'notnull' => true,
                  'default' => 0,
              ]);
              $table_records->addColumn('vacation', 'string', [
			      'notnull' => true,
                  'default' => 0,
              ]);
			  	  
		  			  
			  // Table Description, Tags, projects and Timezone
              $table_records->addColumn('description', 'text', [
                  'notnull' => true,
                  'default' => '',
              ]);
			  $table_records->addColumn('tags', 'text', [
                  'notnull' => true,
                  'default' => '',
              ]);
			  $table_records->addColumn('projects', 'text', [
                  'notnull' => true,
                  'default' => '',
              ]);
              $table_records->addColumn('timezoneoffset', 'integer', [
			      'notnull' => true,
              ]);			  
			  
			  // Keys
              $table_records->setPrimaryKey(['id']);
              $table_records->addIndex(['user_id'], 'timesheet_user_id_index');
          }
		  	  

          return $schema;
      }
  }
