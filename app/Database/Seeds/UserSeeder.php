<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $password = password_hash('password', PASSWORD_DEFAULT);

        $data = [
            'name'     => 'API Admin',
            'email'    => 'admin@example.com',
            'password' => $password,
        ];

        $this->db->table('users')->insert($data);
    }
}
