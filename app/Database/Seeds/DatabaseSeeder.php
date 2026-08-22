<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call('RoleUserSeeder');

        // Sample customers data
        $customers = [];
        $cities = ['Mumbai', 'Delhi', 'Bangalore', 'Chennai', 'Kolkata', 'Hyderabad', 'Pune', 'Ahmedabad'];
        $statuses = ['active', 'inactive', 'pending'];
        $companies = ['Tech Solutions', 'Digital Services', 'Cloud Systems', 'Data Corp', 'Web Innovations', 'Mobile Apps Ltd'];

        for ($i = 1; $i <= 100; $i++) {
            $nameSuffix = '';
            $nameNumber = $i;

            while ($nameNumber > 0) {
                $nameNumber--;
                $nameSuffix = chr(65 + ($nameNumber % 26)) . $nameSuffix;
                $nameNumber = intdiv($nameNumber, 26);
            }

            $customers[] = [
                'name' => 'Customer ' . $nameSuffix,
                'email' => 'customer' . $i . '@example.com',
                'phone' => '917000000000' + $i,
                'company' => $companies[array_rand($companies)],
                'city' => $cities[array_rand($cities)],
                'status' => $statuses[array_rand($statuses)],
                'notes' => 'Sample notes for customer ' . $i,
                'created_at' => date('Y-m-d H:i:s', strtotime('-' . rand(1, 365) . ' days')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-' . rand(1, 30) . ' days')),
            ];
        }

        $this->db->table('customers')->insertBatch($customers);

        // Sample activities
        $activities = [];
        $actions = ['created', 'updated', 'called', 'emailed', 'met', 'quoted'];

        for ($i = 1; $i <= 500; $i++) {
            $action = $actions[array_rand($actions)];
            $activities[] = [
                'customer_id' => rand(1, 100),
                'action' => $action,
                'description' => ucfirst($action) . ' customer record',
                'user_id' => 1,
                'created_at' => date('Y-m-d H:i:s', strtotime('-' . rand(1, 180) . ' days')),
            ];
        }

        $this->db->table('customer_activities')->insertBatch($activities);

        echo "Seeded 100 customers and 500 activities successfully!\n";


        $this->call('CustomerAssignmentSeeder');
    }
}
