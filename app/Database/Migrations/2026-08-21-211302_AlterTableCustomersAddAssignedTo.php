<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterTableCustomersAddAssignedTo extends Migration
{
    public function up()
    {
        $this->forge->addColumn('customers', [
            'assigned_to' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'city',
            ],
        ]);

        $this->forge->addForeignKey(
            'assigned_to',
            'users',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->forge->dropForeignKey('customers', 'assigned_to');
        $this->forge->dropColumn('customers', 'assigned_to');
        
    }
}
