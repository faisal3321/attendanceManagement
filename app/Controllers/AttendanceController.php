<?php

namespace App\Controllers;

use App\Models\AttendanceModel;
use App\Models\CalendarModel;
use App\Models\WorkerModel;
use App\Services\AttendanceSyncService;
use CodeIgniter\RESTful\ResourceController;

class AttendanceController extends ResourceController
{
    protected $format = 'json';

    /**
     * Sync attendance for:
     * - A specific date (if passed)
     * - OR all past dates in calendar (full repair)
     */
    public function syncDailyAttendance($specificDate = null)
    {
        $attendanceService = new AttendanceSyncService();
        $result = $attendanceService->syncDailyAttendance($specificDate);
        
        if (!$result['success']) {
            return $this->fail($result['message'], 400);
        }
        
        return $this->respond([
            'status'  => 200,
            'success' => true,
            'message' => $result['message']
        ]);
    }

    /**
     * Admin override attendance record
     */
    public function adminOverride()
    {
        $attendanceModel = new AttendanceModel();
        $data = $this->request->getJSON(true);

        if (empty($data['id'])) {
            return $this->fail('Attendance ID is required', 400);
        }

        $updateData = [
            'worker_attendance'        => (int) $data['worker_attendance'],
            'customer_side_attendance' => (int) $data['customer_side_attendance'],
        ];

        // Optional punch updates
        if (isset($data['punch_in'])) {
            $updateData['punch_in'] = $data['punch_in'];
        }

        if (isset($data['punch_out'])) {
            $updateData['punch_out'] = $data['punch_out'];
        }

        if ($attendanceModel->update($data['id'], $updateData)) {
            return $this->respond([
                'status'  => 200,
                'success' => true,
                'message' => 'Record successfully updated in database.'
            ]);
        }

        return $this->failServerError('Failed to update database.');
    }

    /**
     * Get attendance list with worker & calendar details
     */
    public function index()
    {
        $attendanceModel = new AttendanceModel();

        $data = $attendanceModel
            ->select('attendance.*, workers.name as worker_name, calendar.calendar_date as actual_date')
            ->join('workers', 'workers.worker_id = attendance.worker_id')
            ->join('calendar', 'calendar.calendar_date = attendance.attendance_date')
            ->orderBy('calendar.calendar_date', 'DESC')
            ->findAll();

        return $this->respond([
            'status'  => 200,
            'success' => true,
            'message' => 'Here is the data you wanted',
            'data'    => $data
        ]);
    }
}