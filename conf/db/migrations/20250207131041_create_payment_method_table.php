<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreatePaymentMethodTable extends AbstractMigration
{
    private string $table_name = '_payment_method';

    public function up(): void
    {
        $table = $this->table($this->table_name, ['id' => $this->table_name . '_id']);
        $table->addColumn($this->table_name . '_new', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn($this->table_name . '_edit', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP', 'null' => TRUE])
              ->addColumn($this->table_name . '_del', 'timestamp', ['null' => TRUE])
              ->addColumn($this->table_name . '_arch', 'timestamp', ['null' => TRUE])
              ->addColumn($this->table_name . '_active', 'boolean', ['default' => TRUE])
              ->addColumn($this->table_name . '_type', 'string', ['limit' => 255, 'null' => TRUE])
              ->addColumn($this->table_name . '_token', 'string', ['limit' => 255, 'null' => TRUE])
              ->addColumn($this->table_name . '_default', 'boolean', ['default' => FALSE])
              ->addColumn($this->table_name . '_billing_address', 'json', ['null' => TRUE, 'comment' => 'Billing address details'])
              ->addIndex([$this->table_name . '_default'], ['name' => 'idx_' . $this->table_name . '_default'])
              ->create();
    }

    public function down(): void
    {
        $this->table($this->table_name)->drop()->save();
    }
}
