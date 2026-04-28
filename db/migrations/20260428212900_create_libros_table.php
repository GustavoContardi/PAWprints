<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateLibrosTable extends AbstractMigration
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
        $table = $this->table('libros');
        $table->addColumn('titulo', 'string', ['limit' => 255])
              ->addColumn('autor', 'string', ['limit' => 255])
              ->addColumn('precio', 'decimal', ['precision' => 10, 'scale' => 2])
              ->addColumn('descripcion', 'text', ['null' => true])
              ->addColumn('stock', 'integer', ['default' => 0])
              ->addColumn('imagen', 'string', ['limit' => 255, 'null' => true])
              ->addColumn('categoria', 'string', ['limit' => 100, 'null' => true])
              ->addColumn('edad', 'string', ['limit' => 50, 'null' => true])
              ->addColumn('es_novedad', 'boolean', ['default' => false])
              ->addColumn('descuento', 'decimal', ['precision' => 5, 'scale' => 2, 'default' => 0])
              ->addColumn('es_recomendado', 'boolean', ['default' => false])
              ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn('updated_at', 'timestamp', ['null' => true])
              ->create();
    }
}
