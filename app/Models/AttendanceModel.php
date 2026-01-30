<?php

namespace App\Models;

use CodeIgniter\Model;

class AttendanceModel extends Model
{
    protected $table            = 'attendance';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    protected $allowedFields    = [
        'worker_id', 
        'attendance_date',
        'worker_attendance', 
        'customer_side_attendance', 
        'punch_in', 
        'punch_out'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getFullAttendance()
    {
        return $this->select('attendance.*, workers.name as worker_name')
                    ->join('workers', 'workers.worker_id = attendance.worker_id')
                    ->orderBy('attendance_date', 'DESC')
                    ->findAll();
    }
}