<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Bookings extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'booking_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'unique'     => true
            ],
            'customer_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'worker_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'duration_months' => [
                'type'       => 'ENUM',
                'constraint' => ['1', '3', '6', '12'],
                'default'    => '1',
            ],
            'created_at' => [
                'type' => 'DATETIME', 
                'null' => true
            ],
            'updated_at' => [
                'type' => 'DATETIME', 
                'null' => true
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('bookings');
    }

    public function down()
    {
        $this->forge->dropTable('bookings');
    }
}