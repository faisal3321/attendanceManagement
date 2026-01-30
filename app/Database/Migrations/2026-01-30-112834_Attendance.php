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
                'default'    => 1,
                'comment'    => ' 0=N/A, 1=Present, 2=Absent, 3=Half-day'
            ],
            'customer_side_attendance' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0, 
                'comment'    => ' 0=N/A, 1=Present, 2=Absent, 3=Half-day'
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