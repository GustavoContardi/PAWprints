<?php

namespace Controllers;

use Models\Employee;
use Core\Session;

class AuthController extends Controller
{
    public function showLogin(array $params): void
    {
        if (Session::isAuthenticated()) {
            header('Location: /admin/reserves');
            exit;
        }

        $this->render('login', [
            'title' => 'Acceso de personal — PAWprints',
            'error' => null,
        ]);
    }

    public function login(array $params): void
    {
        if (!Session::validateCsrf($_POST['csrf_token'] ?? null)) {
            $this->render('login', [
                'title' => 'Acceso de personal — PAWprints',
                'error' => 'Sesión expirada, intentá de nuevo.',
            ]);
            return;
        }

        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';

        $employee = Employee::findByEmail($this->db, $email);

        if ($employee === null || !$employee->verifyPassword($password)) {
            $this->render('login', [
                'title' => 'Acceso de personal — PAWprints',
                'error' => 'Credenciales inválidas',
            ]);
            return;
        }

        Session::login($employee->getId(), $employee->getName());
        header('Location: /admin/reserves');
        exit;
    }

    public function logout(array $params): void
    {
        if (!Session::validateCsrf($_POST['csrf_token'] ?? null)) {
            header('Location: /');
            exit;
        }

        Session::logout();
        header('Location: /');
        exit;
    }
}
