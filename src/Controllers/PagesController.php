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
        $this->render('about', [
            'title' => 'Sobre Nosotros — PAWprints',
            'styles' => ['sobreNosotros.css']
        ]);
    }

    public function contact(array $params): void
    {
        $this->render('contact', [
            'title' => 'Contacto — PAWprints',
            'styles' => ['contacto.css']
        ]);
    }

    public function indispensables(array $params): void
    {
        $collection = new \Models\BooksCollection($this->db);

        $this->render('special', [
            'title'       => 'Indispensables — PAWprints',
            'styles'      => ['indispensables.css'],
            'new'         => $collection->getNovedades(4),
            'sales'       => $collection->getDescuentos(4),
            'recommended' => $collection->getRecomendados(4)
        ]);
    }
}