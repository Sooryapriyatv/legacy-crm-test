<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUniqueCustomerContactIndexes extends Migration
{
    public function up()
    {
        $this->db->query(
            'ALTER TABLE `customers`
             ADD UNIQUE KEY `customers_email_unique` (`email`),
             ADD UNIQUE KEY `customers_phone_unique` (`phone`)'
        );
    }

    public function down()
    {
        $this->db->query(
            'ALTER TABLE `customers`
             DROP INDEX `customers_email_unique`,
             DROP INDEX `customers_phone_unique`'
        );
    }
}
