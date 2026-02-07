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
     * Sync attendance for:
     * - A specific date (if passed)
     * - OR all past dates in calendar (full repair)
     */
    public function syncDailyAttendance($specificDate = null)
    {
        $attendanceModel = new AttendanceModel();
        $workerModel     = new WorkerModel();
        $calendarModel   = new CalendarModel();

        $today = date('Y-m-d');

        // Prevent syncing future dates
        if ($specificDate && $specificDate > $today) {
            return $this->failValidationError('Cannot sync future dates.');
        }

        // Only active workers
        $workers = $workerModel->where('status', 'active')->findAll();

        // Decide which dates to sync
        $datesToSync = $specificDate
            ? [['calendar_date' => $specificDate]]
            : $calendarModel
                ->where('calendar_date <=', $today)
                ->findAll();

        $syncCount = 0;

        foreach ($datesToSync as $dateEntry) {
            $date = $dateEntry['calendar_date'];

            foreach ($workers as $worker) {

                $exists = $attendanceModel->where([
                    'worker_id'       => $worker['worker_id'], // adjust if your PK is `id`
                    'attendance_date' => $date,
                ])->first();

                if (!$exists) {
                    $attendanceModel->insert([
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

        return $this->respond([
            'status'  => 200,
            'success' => true,
            'message' => "Synced $syncCount missing records."
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
            return $this->failValidationError('Attendance ID is required.');
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
            'data'    => $data
        ]);
    }
}
