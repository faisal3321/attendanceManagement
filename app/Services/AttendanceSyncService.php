<?php

namespace App\Services;

use App\Models\AttendanceModel;
use App\Models\WorkerModel;
use App\Models\CalendarModel;

class AttendanceSyncService
{
    protected $attendanceModel;
    protected $workerModel;
    protected $calendarModel;
    
    public function __construct()
    {
        $this->attendanceModel = new AttendanceModel();
        $this->workerModel = new WorkerModel();
        $this->calendarModel = new CalendarModel();
    }
    
    /**
     * Sync attendance for a specific date
     */
    public function syncDailyAttendance($specificDate = null)
    {
        $today = date('Y-m-d');
        
        // Prevent syncing future dates
        if ($specificDate && $specificDate > $today) {
            return ['success' => false, 'message' => 'Cannot sync future dates'];
        }
        
        // Only active workers
        $workers = $this->workerModel->where('status', 'active')->findAll();
        
        // Decide which dates to sync
        if ($specificDate) {
            // Check if date exists in calendar
            $dateExists = $this->calendarModel->where('calendar_date', $specificDate)->first();
            if (!$dateExists) {
                return ['success' => false, 'message' => 'Date not found in calendar'];
            }
            $datesToSync = [['calendar_date' => $specificDate]];
        } else {
            // Sync all past dates
            $datesToSync = $this->calendarModel
                ->where('calendar_date <=', $today)
                ->findAll();
        }
        
        $syncCount = 0;
        
        foreach ($datesToSync as $dateEntry) {
            $date = $dateEntry['calendar_date'];
            
            foreach ($workers as $worker) {
                $exists = $this->attendanceModel->where([
                    'worker_id'       => $worker['worker_id'],
                    'attendance_date' => $date,
                ])->first();
                
                if (!$exists) {
                    $this->attendanceModel->insert([
                        'worker_id'                => $worker['worker_id'],
                        'attendance_date'          => $date,
                        'worker_attendance'        => 1,
                        'customer_side_attendance' => 0,
                        'punch_in'                 => '08:00:00',
                        'punch_out'                => '20:00:00'
                    ]);
                    $syncCount++;
                }
            }
        }
        
        return [
            'success' => true,
            'count' => $syncCount,
            'message' => "Synced $syncCount missing records."
        ];
    }
}