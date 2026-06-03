<?php

namespace Controllers;

class PagesController extends Controller
{
    public function index(array $params): void
    {
        $this->render('home', [
            'title' => 'PAWprints — Libros',
            'styles' => ['index.css']
        ]);
    }

    public function about(array $params): void
    {
        $microdata = [
            [
                '@context' => 'https://schema.org',
                '@type' => 'AboutPage',
                'name' => 'Sobre Nosotros — PAWprints',
                'url' => 'https://pawprints.com/about-us'
            ],
            [
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
            ]
        ];

        $this->render('about', [
            'title' => 'Sobre Nosotros — PAWprints',
            'styles' => ['sobreNosotros.css'],
            'microdata' => $microdata
        ]);
    }

    public function contact(array $params): void
    {
        $microdata = [
            [
                '@context' => 'https://schema.org',
                '@type' => 'ContactPage',
                'name' => 'Contacto — PAWprints',
                'url' => 'https://pawprints.com/contact'
            ],
            [
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
            ]
        ];

        $this->render('contact', [
            'title' => 'Contacto — PAWprints',
            'styles' => ['contacto.css'],
            'microdata' => $microdata
        ]);
    }

    public function special(array $params): void
    {
        $collection = new \Models\BooksCollection($this->db);

        $newBooks = $collection->getNew(4);
        $salesBooks = $collection->getSales(4);
        $recommendedBooks = $collection->getRecommended(4);

        // Build ItemList microdata
        $itemListElements = [];
        $position = 1;

        foreach ($newBooks as $book) {
            $itemListElements[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'item' => [
                    '@type' => 'Book',
                    'name' => $book['title'],
                    'url' => 'https://pawprints.com/book/' . $book['id'],
                    'author' => ['@type' => 'Person', 'name' => $book['author']],
                    'offers' => ['@type' => 'Offer', 'price' => $book['price'], 'priceCurrency' => 'ARS']
                ]
            ];
        }

        foreach ($salesBooks as $book) {
            $itemListElements[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'item' => [
                    '@type' => 'Book',
                    'name' => $book['title'],
                    'url' => 'https://pawprints.com/book/' . $book['id'],
                    'author' => ['@type' => 'Person', 'name' => $book['author']],
                    'offers' => ['@type' => 'Offer', 'price' => $book['price'], 'priceCurrency' => 'ARS']
                ]
            ];
        }

        foreach ($recommendedBooks as $book) {
            $itemListElements[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'item' => [
                    '@type' => 'Book',
                    'name' => $book['title'],
                    'url' => 'https://pawprints.com/book/' . $book['id'],
                    'author' => ['@type' => 'Person', 'name' => $book['author']],
                    'offers' => ['@type' => 'Offer', 'price' => $book['price'], 'priceCurrency' => 'ARS']
                ]
            ];
        }

        $microdata = [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => 'Indispensables — PAWprints',
            'itemListElement' => $itemListElements
        ];

        $this->render('special', [
            'title'       => 'Indispensables — PAWprints',
            'styles'      => ['indispensables.css'],
            'new'         => $newBooks,
            'sales'       => $salesBooks,
            'recommended' => $recommendedBooks,
            'microdata'    => $microdata
        ]);
    }
}