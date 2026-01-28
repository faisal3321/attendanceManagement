<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Calendar extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'             => [
                'type'              => 'INT',
                'constraint'        => 11,
                'unsigned'          => true,
                'auto_increment'    => true
            ],
            'calendar_id'    => [
                'type'              => 'VARCHAR',
                'constraint'        => 20,
                'unique'            => true,
                'comment'           => 'Format: CAL-YYYYMMDD'
            ],
            'calendar_date'  => [
                'type'              => 'DATE',
            ],
            'day'            => [
                'type'              => 'VARCHAR',
                'constraint'        => 20
            ],
            'month'          => [
                'type'              => 'VARCHAR',
                'constraint'        => 20
            ],
            'year'           => [
                'type'              => 'YEAR',
                'constraint'        => 4
            ],
            'is_weekend'    => [
                'type'              => 'TINYINT',
                'constraint'        => 1,
                'default'           => 0,
                'comment'           => '0 = weekdays, 1 = weekend'
            ],
            'created_at'    => [
                'type'              => 'DATETIME',
                'null'              => true,
            ],
            'updated_at'    => [
                'type'              => 'DATETIME',
                'null'              => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('calendar_date');
        $this->forge->createTable('calendar');
    }

    public function down()
    {
        //
    }
}
