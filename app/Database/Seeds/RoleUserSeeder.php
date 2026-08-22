<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RoleUserSeeder extends Seeder
{
    public function run()
    {
        $users = $this->db->table('users');

        $users->insert([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => password_hash('Admin@123', PASSWORD_DEFAULT),
            'role' => 'admin',
        ]);

        $users->insert([
            'name' => 'manager',
            'email' => 'manager@example.com',
            'password' => password_hash('Manager@123', PASSWORD_DEFAULT),
            'role' => 'manager',
        ]);
        $managerId = $this->db->insertID();

        $users->insertBatch([
            [
                'name' => 'sales1',
                'email' => 'sales1@example.com',
                'password' => password_hash('Sales@123', PASSWORD_DEFAULT),
                'role' => 'sales',
                'manager_id' => $managerId,
            ],
            [
                'name' => 'sales2',
                'email' => 'sales2@example.com',
                'password' => password_hash('Sales@123', PASSWORD_DEFAULT),
                'role' => 'sales',
                'manager_id' => $managerId,
            ],
        ]);
    }
}
