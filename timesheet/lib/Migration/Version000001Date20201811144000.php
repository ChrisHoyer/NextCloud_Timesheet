<?php

  namespace OCA\Timesheet\Migration;

  use Closure;
  use OCP\DB\ISchemaWrapper;
  use OCP\Migration\SimpleMigrationStep;
  use OCP\Migration\IOutput;

  class Version000002Date20201811144000 extends SimpleMigrationStep {

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
              $table = $schema->createTable('timesheet_records');
              $table->addColumn('id', 'integer', [
                  'autoincrement' => true,
                  'notnull' => true,
              ]);
              $table->addColumn('title', 'string', [
                  'notnull' => true,
                  'length' => 200,
              ]);
              $table->addColumn('user_id', 'string', [
                  'notnull' => true,
                  'length' => 200,
              ]);
              $table->addColumn('content', 'text', [
                  'notnull' => true,
                  'default' => '',
              ]);

              $table->setPrimaryKey(['id']);
              $table->addIndex(['user_id'], 'timesheet_user_id_index');
          }

          return $schema;
      }
  }