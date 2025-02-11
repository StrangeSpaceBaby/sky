<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreatePaymentHistoryTable extends AbstractMigration
{
    private string $table_name = '_payment_history';

    public function up(): void
    {
        $table = $this->table($this->table_name, ['id' => $this->table_name . '_id']);
        $table->addColumn($this->table_name . '_new', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn($this->table_name . '_edit', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP', 'null' => TRUE])
              ->addColumn($this->table_name . '_del', 'timestamp', ['null' => TRUE])
              ->addColumn($this->table_name . '_arch', 'timestamp', ['null' => TRUE])
              ->addColumn($this->table_name . '_active', 'boolean', ['default' => TRUE])
              ->addColumn('fk__mem_id', 'integer', ['null' => TRUE])
              ->addColumn('fk__sub_plan_id', 'integer', ['null' => TRUE])
              ->addColumn('fk__enroll_id', 'integer', ['null' => TRUE])
              ->addColumn($this->table_name . '_type', 'enum', ['values' => ['all', 'subscription', 'purchases'], 'default' => 'all'])
              ->addColumn($this->table_name . '_date_start', 'date', ['null' => TRUE])
              ->addColumn($this->table_name . '_date_end', 'date', ['null' => TRUE])
              ->addIndex([$this->table_name . '_type'], ['name' => 'idx_' . $this->table_name . '_type'])
              ->create();
    }

    public function down(): void
    {
        $this->table($this->table_name)->drop()->save();
    }
}
