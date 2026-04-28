<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class LibroSeeder extends AbstractSeed
{
    public function run(): void
    {
        $data = [
            [
                'titulo'         => 'El Aleph',
                'autor'          => 'Jorge Luis Borges',
                'precio'         => 15000.00,
                'descripcion'    => 'Una de las obras maestras de la literatura universal.',
                'stock'          => 10,
                'imagen'         => 'aleph.jpg',
                'categoria'      => 'literatura',
                'edad'           => 'adulto',
                'es_novedad'     => true,
                'descuento'      => 10.00,
                'es_recomendado' => true,
            ],
            [
                'titulo'         => 'Rayuela',
                'autor'          => 'Julio Cortázar',
                'precio'         => 18500.00,
                'descripcion'    => 'Una novela que se puede leer de muchas maneras.',
                'stock'          => 5,
                'imagen'         => 'rayuela.jpg',
                'categoria'      => 'literatura',
                'edad'           => 'adulto',
                'es_novedad'     => false,
                'descuento'      => 0.00,
                'es_recomendado' => true,
            ],
            [
                'titulo'         => 'Cien años de soledad',
                'autor'          => 'Gabriel García Márquez',
                'precio'         => 22000.00,
                'descripcion'    => 'La saga de la familia Buendía en Macondo.',
                'stock'          => 8,
                'imagen'         => 'soledad.jpg',
                'categoria'      => 'literatura',
                'edad'           => 'adulto',
                'es_novedad'     => true,
                'descuento'      => 15.00,
                'es_recomendado' => false,
            ]
        ];

        $posts = $this->table('libros');
        $posts->insert($data)
              ->saveData();
    }
}
