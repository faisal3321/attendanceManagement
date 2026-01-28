<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Workers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                => [
                'type'                  => 'INT',
                'constraint'            => 11,
                'unsigned'              => true,
                'auto_increment'        => true
            ],
            'worker_id'         => [
                'type'                  => 'VARCHAR',
                'constraint'            => 10,
                'unique'                => true
            ],
            'name'              => [
                'type'                  => 'VARCHAR',
                'constraint'            => 150
            ],
            'age'               => [
                'type'                  => 'INT',
                'constraint'            => 3
            ],
            'gender'            => [
                'type'                  => 'ENUM',
                'constraint'            => ['male', 'female', 'others']
            ],
            'phone'             => [
                'type'                  => 'VARCHAR',
                'constraint'            => 20,
                'unique'                => true
            ],
            'address'           => [
                'type'                  => 'TEXT',
                'null'                  => true
            ],
            'status'            => [
                'type'                  => 'ENUM',
                'constraint'            => ['active', 'inactive'],
                'default'               => 'active'
            ],
            'created_by'        => [
                'type'                  => 'INT',
                'constraint'            => 11,
                'unsigned'              => true
            ],
            'created_at'        => [
                'type'                  => 'DATETIME',
                'null'                  => true,
            ],
            'updated_at'        => [
                'type'                  => 'DATETIME',
                'null'                  => true,
            ],

        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('workers');
    }

    public function down()
    {
        //
    }
}
