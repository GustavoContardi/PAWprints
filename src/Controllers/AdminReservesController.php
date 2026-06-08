<?php

namespace Controllers;

use Models\ReservesCollection;

class AdminReservesController extends Controller
{
    public function index(array $params): void
    {
        // 1. Authenticate user
        $this->requireAuth();

        // 2. Read query filters
        $search = trim($_GET['search'] ?? '');

        // 3. Fetch results
        $collection = new ReservesCollection($this->db);
        $result = $collection->getAll(['search' => $search]);

        // 4. Render view
        $this->render('admin_reserves', [
            'title'    => 'Pedidos — PAWprints',
            'reserves' => $result['items'],
            'total'    => $result['total'],
            'search'   => $search,
        ]);
    }
}
