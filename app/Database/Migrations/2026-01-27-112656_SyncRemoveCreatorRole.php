<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SyncRemoveCreatorRole extends Migration
{
    public function up()
    {
        //
    }

    public function down()
    {
    $this->forge->addColumn('workers', [
        'creator_role' => [
            'type'       => 'ENUM',
            'constraint' => ['admin', 'super_admin'],
            'null'       => false,
            'after'      => 'created_by',
        ],
    ]);
    }
}
