<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateEnrollTable extends AbstractMigration
{
    private string $table_name = '_enroll';

    public function up(): void
    {
        $table = $this->table($this->table_name, ['id' => $this->table_name . '_id']);
        $table->addColumn('fk__sub_plan_id', 'integer', ['null' => false])
              ->addColumn('fk__mem_id', 'integer', ['null' => false])
              ->addColumn('mem_fname', 'string', ['limit' => 255, 'null' => false])
              ->addColumn('mem_lname', 'string', ['limit' => 255, 'null' => false])
              ->addColumn('mem_email', 'string', ['limit' => 255, 'null' => false])
              ->addColumn('mem_phone', 'string', ['limit' => 20, 'null' => false])
              ->addColumn('billing_street', 'string', ['limit' => 255, 'null' => false])
              ->addColumn('billing_street2', 'string', ['limit' => 255, 'null' => true])
              ->addColumn('billing_city', 'string', ['limit' => 100, 'null' => false])
              ->addColumn('billing_state', 'string', ['limit' => 100, 'null' => false])
              ->addColumn('billing_postal', 'string', ['limit' => 20, 'null' => false])
              ->addColumn('cc_number', 'string', ['limit' => 20, 'null' => false])
              ->addColumn('cvv', 'string', ['limit' => 5, 'null' => false])
              ->addColumn('expiration', 'string', ['limit' => 10, 'null' => false])
              ->addColumn($this->table_name . '_new', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn($this->table_name . '_edit', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP', 'null' => true])
              ->addColumn($this->table_name . '_del', 'timestamp', ['null' => true])
              ->addColumn($this->table_name . '_arch', 'timestamp', ['null' => true])
              ->addColumn($this->table_name . '_active', 'boolean', ['default' => true])
			  ->addColumn($this->table_name . '_ulid', 'string', ['limit' => 26, 'null' => TRUE])
			  ->addIndex([$this->table_name . '_ulid'], ['name' => 'idx_' . $this->table_name . '_ulid'])              
			  ->create();
    }

    public function down(): void
    {
        $this->table($this->table_name)->drop()->save();
    }
}