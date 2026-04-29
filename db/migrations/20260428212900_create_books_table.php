<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateBooksTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $table = $this->table('books');
        $table->addColumn('title', 'string', ['limit' => 255])
              ->addColumn('author', 'string', ['limit' => 255])
              ->addColumn('price', 'decimal', ['precision' => 10, 'scale' => 2])
              ->addColumn('description', 'text', ['null' => true])
              ->addColumn('stock', 'integer', ['default' => 0])
              ->addColumn('image', 'string', ['limit' => 255, 'null' => true])
              ->addColumn('category', 'string', ['limit' => 100, 'null' => true])
              ->addColumn('age', 'string', ['limit' => 50, 'null' => true])
              ->addColumn('is_new', 'boolean', ['default' => false])
              ->addColumn('discount', 'decimal', ['precision' => 5, 'scale' => 2, 'default' => 0])
              ->addColumn('is_recommended', 'boolean', ['default' => false])
              ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn('updated_at', 'timestamp', ['null' => true])
              ->create();
    }
}
