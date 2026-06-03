<?php

namespace Controllers;

use Core\View;

class HomeController
{
    public function index(array $params): void
    {
        $microdata = [
            '@context' => 'https://schema.org',
            '@type' => 'BookStore',
            'name' => 'PAWprints libros S.A.',
            'url' => 'https://pawprints.com',
            'telephone' => '+54-11-1234-5678',
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => 'Humberto Primo 666',
                'addressLocality' => 'Luján',
                'addressRegion' => 'Buenos Aires',
                'postalCode' => '6700',
                'addressCountry' => 'AR'
            ],
            'sameAs' => [
                'https://www.instagram.com/pawprints',
                'https://www.x.com/pawprints',
                'https://www.youtube.com/pawprints'
            ]
        ];

        View::render('home', [
            'title'  => 'PAWprints — Libros',
            'styles' => ['index.css'],
            'microdata' => $microdata,
        ]);
    }
}