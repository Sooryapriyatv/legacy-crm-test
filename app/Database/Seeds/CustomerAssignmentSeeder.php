<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CustomerAssignmentSeeder extends Seeder
{
    public function run()
    {
        $users = $this->db->table('users')
            ->whereIn('name', ['sales1', 'sales2'])
            ->get()
            ->getResultArray();

        if (count($users) < 2) {
            return;
        }

        $sales1Id = null;
        $sales2Id = null;

        foreach ($users as $user) {
            if ($user['name'] === 'sales1') {
                $sales1Id = $user['id'];
            }

            if ($user['name'] === 'sales2') {
                $sales2Id = $user['id'];
            }
        }

        $customers = $this->db->table('customers')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($customers as $index => $customer) {

            $assignedTo = ($index % 2 === 0) ? $sales1Id : $sales2Id;

            $this->db->table('customers')
                ->where('id', $customer['id'])
                ->update([
                    'assigned_to' => $assignedTo,
                ]);
        }
    }
}