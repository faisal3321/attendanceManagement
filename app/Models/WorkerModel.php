<?php

namespace App\Models;

use CodeIgniter\Model;

class WorkerModel extends Model
{
    protected $table            = 'workers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $allowedFields    = [
        'worker_id',
        'name',
        'age',
        'gender',
        'phone',
        'address',
        'status',
        'created_by',
        'creator_role',
    ];

   

    // Dates
    protected $useTimestamps = true;
   
}
