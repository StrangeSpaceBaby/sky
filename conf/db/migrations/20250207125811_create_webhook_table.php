<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateWebhookTable extends AbstractMigration
{
    private string $table_name = '_webhook';

    public function up(): void
    {
        $table = $this->table($this->table_name, ['id' => $this->table_name . '_id']);
        $table->addColumn($this->table_name . '_new', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn($this->table_name . '_edit', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP', 'null' => TRUE])
              ->addColumn($this->table_name . '_del', 'timestamp', ['null' => TRUE])
              ->addColumn($this->table_name . '_arch', 'timestamp', ['null' => TRUE])
              ->addColumn($this->table_name . '_active', 'boolean', ['default' => TRUE])
              ->addColumn('fk__vendor_id', 'integer', ['null' => TRUE])
              ->addColumn($this->table_name . '_type', 'enum', ['values' => ['inbound_billing'], 'default' => 'inbound_billing'])
              ->addColumn($this->table_name . '_payload', 'json', ['null' => TRUE, 'comment' => ''])
              ->addIndex([$this->table_name . '_type'], ['name' => 'idx_' . $this->table_name . '_type'])
              ->create();
    }

    public function down(): void
    {
        $this->table($this->table_name)->drop()->save();
    }
}
