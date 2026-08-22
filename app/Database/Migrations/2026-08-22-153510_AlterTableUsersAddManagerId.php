<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterTableUsersAddManagerId extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'manager_id' => [
            'type'       => 'INT',
            'constraint' => 11,
            'unsigned'   => true,
            'null'       => true,
        ],
        ]);

        $this->db->query("
            ALTER TABLE users
            ADD CONSTRAINT fk_users_manager
            FOREIGN KEY (manager_id)
            REFERENCES users(id)
            ON DELETE SET NULL
            ON UPDATE CASCADE
        ");
    }

    public function down()
    {
        $this->db->query("
            ALTER TABLE users
            DROP FOREIGN KEY fk_users_manager
        ");

        $this->forge->dropColumn('users', 'manager_id');
    }
}
