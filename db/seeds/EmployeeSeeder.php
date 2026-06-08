<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class EmployeeSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run(): void
    {
        $this->execute('DELETE FROM employees');

        $email = $_ENV['EMPLOYEE_EMAIL'] ?? 'empleado@pawprints.com';
        $password = $_ENV['EMPLOYEE_PASSWORD'] ?? 'changeme';

        $normalizedEmail = strtolower(trim($email));
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        $data = [
            [
                'name' => 'Personal PAWprints',
                'email' => $normalizedEmail,
                'password_hash' => $passwordHash,
            ]
        ];

        $this->table('employees')->insert($data)->saveData();
    }
}
