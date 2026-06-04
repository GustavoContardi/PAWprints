<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateReservesTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('reserves');
        $table->addColumn('book_id', 'integer', ['null' => true])
              ->addColumn('book_title', 'string', ['limit' => 255, 'null' => true])
              ->addColumn('buyer_name', 'string', ['limit' => 255])
              ->addColumn('phone', 'string', ['limit' => 50])
              ->addColumn('email', 'string', ['limit' => 255])
              ->addColumn('copies', 'integer', ['default' => 1])
              ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
              ->addForeignKey('book_id', 'books', 'id', ['delete' => 'SET NULL', 'update' => 'CASCADE'])
              ->create();
    }
}
