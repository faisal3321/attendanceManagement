<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Attendance extends Migration
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
            ],
            'customer_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'worker_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'attendance_date' => [
                'type' => 'DATE'
            ],
            'worker_attendance' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1, // Admin side default: Present
            ],
            'customer_side_attendance' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1, // Customer side default: Present
            ],
            'discrepancy' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0, // 0 = Matches, 1 = Conflict
            ],
            'punch_in'   => [
                'type' => 'TIME', 
                'default' => '08:00:00'
            ],
            'punch_out'  => [
                'type' => 'TIME', 
                'default' => '20:00:00'
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
        $this->forge->addKey(['booking_id', 'attendance_date']);
        $this->forge->createTable('attendance');
    }

    public function down()
    {
        $this->forge->dropTable('attendance');
    }
}