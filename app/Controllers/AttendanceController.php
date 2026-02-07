<?php

namespace App\Controllers;

use App\Models\AttendanceModel;
use App\Models\CalendarModel;
use App\Models\WorkerModel;
use CodeIgniter\RESTful\ResourceController;

class AttendanceController extends ResourceController
{
    protected $format = 'json';

    /**
     * FIX PROBLEM 2: Optimized to handle any date passed to it.
     * This allows the system to sync past, present, or future dates.
     */
    public function syncDailyAttendance($specificDate = null)
    {
        $attendanceModel = new AttendanceModel();
        $workerModel     = new WorkerModel();
        $calendarModel   = new CalendarModel();

        $workers = $workerModel->where('status', 'active')->findAll();
        
        // If a specific date is passed, just sync that one
        // If NOT, we sync for all dates in the calendar table (Full Repair)
        $datesToSync = $specificDate 
            ? [['calendar_date' => $specificDate]] 
            : $calendarModel->findAll();

        $syncCount = 0;
        foreach ($datesToSync as $dateEntry) {
            $date = $dateEntry['calendar_date'];
            
            foreach ($workers as $worker) {
                $exists = $attendanceModel->where([
                    'worker_id'       => $worker['worker_id'],
                    'attendance_date' => $date 
                ])->first();

                if (!$exists) {
                    $attendanceModel->insert([
                        'worker_id'         => $worker['worker_id'],
                        'attendance_date'   => $date,
                        'worker_attendance' => 1, 
                        'punch_in'          => '08:00:00',
                        'punch_out'         => '20:00:00'
                    ]);
                    $syncCount++;
                }
            }
        }

        return $this->respond([
            'status'  => 200,
            'message' => "Synced $syncCount missing records."
        ]);
    }

    public function index()
    {
        $attendanceModel = new AttendanceModel();
        
        // FIX PROBLEM 3: Joining on the DATE string now that both tables 
        // use 'YYYY-MM-DD' format
        $data = $attendanceModel->select('attendance.*, workers.name as worker_name, calendar.calendar_date as actual_date')
            ->join('workers', 'workers.worker_id = attendance.worker_id')
            ->join('calendar', 'calendar.calendar_date = attendance.attendance_date')
            ->orderBy('calendar.calendar_date', 'DESC')
            ->findAll();

        return $this->respond(['status' => 200, 'success' => true, 'data' => $data]);
    }
}