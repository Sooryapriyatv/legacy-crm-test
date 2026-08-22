<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterTableUsersAddRole extends Migration
{
    public function up()
    {
        $fields = [
            'role' => [
                'type'       => 'ENUM',
                'constraint' => ['admin', 'manager', 'sales'],
                'default'    => 'sales',
                'null'       => false,
                'after'      => 'password',
            ],
        ];

        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'role');
    }
}

    
