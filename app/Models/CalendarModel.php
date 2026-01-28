<?php

namespace App\Models;

use CodeIgniter\Model;

class CalendarModel extends Model
{
    protected $table            = 'calendar';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields    = [
        'calendar_id', 
        'calendar_date', 
        'day', 
        'month', 
        'year', 
        'is_weekend', 
        'created_at', 
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
  
}
