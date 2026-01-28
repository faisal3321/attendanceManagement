<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Customer extends Migration
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
            'name'           => [
                'type'          => 'VARCHAR', 
                'constraint'    => 100,
                'null'          => false
            ],
            'email'          => [
                'type'          => 'VARCHAR', 
                'constraint'    => 100, 
                'unique'        => true,
                'null'          => false
            ],
            'password'       => [
                'type'          => 'VARCHAR', 
                'constraint'    => 255,
                'null'          => false
            ], 
            'phone'          => [
                'type'          => 'VARCHAR', 
                'constraint'    => 20, 
                'unique'        => true,
                'null'          => false
            ],
            'created_at'     => [
                'type'          => 'DATETIME', 
                'null'          => true
            ],
            'updated_at'     => [
                'type'          => 'DATETIME', 
                'null'          => true
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('customers');
    }

    public function down()
    {
        //
    }
}
