<?php
// src/Controllers/HomeController.php

namespace Controllers;

class HomeController
{
    public function index(array $params): void
    {
        require __DIR__ . '/../Views/home.php';
    }
}