<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class BookSeeder extends AbstractSeed
{
    public function run(): void
    {
        $data = [
            [
                'title'          => 'El Aleph',
                'author'         => 'Jorge Luis Borges',
                'price'          => 15000.00,
                'description'    => 'Una de las obras maestras de la literatura universal.',
                'stock'          => 10,
                'image'          => 'aleph.jpg',
                'category'       => 'literatura',
                'age'            => 'adulto',
                'is_new'         => true,
                'discount'       => 10.00,
                'is_recommended' => true,
            ],
            [
                'title'          => 'Rayuela',
                'author'         => 'Julio Cortázar',
                'price'          => 18500.00,
                'description'    => 'Una novela que se puede leer de muchas maneras.',
                'stock'          => 5,
                'image'          => 'rayuela.jpg',
                'category'       => 'literatura',
                'age'            => 'adulto',
                'is_new'         => false,
                'discount'       => 0.00,
                'is_recommended' => true,
            ],
            [
                'title'          => 'Cien años de soledad',
                'author'         => 'Gabriel García Márquez',
                'price'          => 22000.00,
                'description'    => 'La saga de la familia Buendía en Macondo.',
                'stock'          => 8,
                'image'          => 'soledad.jpg',
                'category'       => 'literatura',
                'age'            => 'adulto',
                'is_new'         => true,
                'discount'       => 15.00,
                'is_recommended' => false,
            ]
        ];

        $posts = $this->table('books');
        $posts->insert($data)
              ->saveData();
    }
}
