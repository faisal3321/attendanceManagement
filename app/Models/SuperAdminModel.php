<?php

namespace App\Models;

use CodeIgniter\Model;

class SuperAdminModel extends Model
{
    protected $table            = 'superAdmin';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields    = ['username', 'password', 'is_super_admin', 'created_at', 'updated_at'];

    protected $useTimestamps = true;

}
