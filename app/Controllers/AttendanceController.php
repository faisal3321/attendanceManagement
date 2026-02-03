<?php

namespace App\Controllers;

use App\Models\AttendanceModel;
use App\Models\CalendarModel;
use App\Models\WorkerModel;
use CodeIgniter\RESTful\ResourceController;

class AttendanceController extends ResourceController
{
    protected $format = 'json';

    public function syncDailyAttendance()
    {
        $attendanceModel = new AttendanceModel();
        $workerModel     = new WorkerModel();
        $calendarModel   = new CalendarModel();

        $today = date('Y-m-d');
        $calendarEntry = $calendarModel->where('calendar_date', $today)->first();

        if (!$calendarEntry) {
            return $this->fail("Please add today's date ($today) to your Calendar table first.");
        }

        $calendarId = $calendarEntry['id']; 
        $workers = $workerModel->findAll();
        $syncCount = 0;

        foreach ($workers as $worker) {
            $exists = $attendanceModel->where([
                'worker_id'       => $worker['worker_id'],
                'attendance_date' => $calendarId 
            ])->first();

            if (!$exists) {
                $attendanceModel->insert([
                    'worker_id'                => $worker['worker_id'],
                    'attendance_date'          => $calendarId, 
                    'worker_attendance'        => 1, 
                    'customer_side_attendance' => 0, 
                    'punch_in'                 => '08:00:00',
                    'punch_out'                => '20:00:00'
                ]);
                $syncCount++;
            }
        }

        return $this->respond([
            'status'  => 200,
            'success' => true,
            'message' => "Synced $syncCount workers for Calendar ID: $calendarId ($today)."
        ]);
    }

    public function adminOverride()
    {
        $attendanceModel = new AttendanceModel();
        $data = $this->request->getJSON(true);

        if (empty($data['id'])) {
            return $this->failValidationError("Attendance ID is required.");
        }

        // Logic fix: Ensure we take the values from request directly to update DB
        $updateData = [
            'worker_attendance'        => (int)$data['worker_attendance'],
            'customer_side_attendance' => (int)$data['customer_side_attendance']
        ];

        // Keep existing times if not provided in the update request
        if (isset($data['punch_in'])) $updateData['punch_in'] = $data['punch_in'];
        if (isset($data['punch_out'])) $updateData['punch_out'] = $data['punch_out'];

        if ($attendanceModel->update($data['id'], $updateData)) {
            return $this->respond([
                'status'  => 200, 
                'success' => true, 
                'message' => "Record successfully updated in database."
            ]);
        }
        return $this->failServerError("Failed to update database.");
    }

    public function index()
    {
        $attendanceModel = new AttendanceModel();
        $data = $attendanceModel->select('attendance.*, workers.name as worker_name, calendar.calendar_date as actual_date')
                                ->join('workers', 'workers.worker_id = attendance.worker_id')
                                ->join('calendar', 'calendar.date = attendance.attendance_date')
                                ->orderBy('calendar.calendar_date', 'DESC')
                                ->findAll();
        echo (string)$attendanceModel->db->getLastQuery();
        die();
        return $this->respond(['status' => 200, 'success' => true, 'data' => $data]);
    }
}