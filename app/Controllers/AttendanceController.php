<?php

namespace App\Controllers;

use App\Models\AttendanceModel;
use App\Models\BookingModel;
use App\Models\CalendarModel;
use CodeIgniter\RESTful\ResourceController;

class AttendanceController extends ResourceController
{
    protected $format = 'json';

    /**
     * MAIN SYNC TASK: 
     * This takes the date from the Calendar and creates attendance for all active bookings.
     */
    public function syncDailyAttendance()
    {
        $attendanceModel = new AttendanceModel();
        $bookingModel    = new BookingModel();
        $calendarModel   = new CalendarModel();

        // 1. Get today's date from the Calendar table
        $today = date('Y-m-d');
        $calendarDate = $calendarModel->where('calendar_date', $today)->first();

        if (!$calendarDate) {
            return $this->fail("Calendar date for today not found. Run Calendar index first.");
        }

        $targetDate = $calendarDate['calendar_date'];

        // 2. Get all bookings to generate daily attendance
        $activeBookings = $bookingModel->findAll();
        $syncCount = 0;

        foreach ($activeBookings as $booking) {
            // Check if record exists for this booking on this calendar date
            $exists = $attendanceModel->where([
                'booking_id'      => $booking['booking_id'],
                'attendance_date' => $targetDate
            ])->first();

            if (!$exists) {
                $attendanceModel->insert([
                    'booking_id'               => $booking['booking_id'],
                    'customer_id'              => $booking['customer_id'],
                    'worker_id'                => $booking['worker_id'],
                    'attendance_date'          => $targetDate,
                    'worker_attendance'        => 1, // Default Present
                    'customer_side_attendance' => 1, // Default Present
                    'discrepancy'              => 0, // No conflict
                    'punch_in'                 => '08:00:00',
                    'punch_out'                => '20:00:00'
                ]);
                $syncCount++;
            }
        }

        return $this->respond([
            'status'  => 200,
            'success' => true,
            'message' => "Attendance synced with Calendar. $syncCount new records created for $targetDate."
        ]);
    }

    /**
     * CUSTOMER UPDATE:
     * Customer can only change 'customer_side_attendance'.
     * The system automatically handles the discrepancy flag.
     */
    public function updateCustomerStatus()
    {
        $attendanceModel = new AttendanceModel();
        $data = $this->request->getJSON(true);

        if (empty($data['id'])) {
            return $this->failValidationError("Attendance ID is required.");
        }

        // 1. Fetch current record
        $record = $attendanceModel->find($data['id']);
        if (!$record) return $this->failNotFound("Attendance record not found.");

        $newCustomerStatus = (int)$data['customer_side_attendance']; // Expects 0 or 1

        // 2. Logic: If worker_attendance (Admin) doesn't match new customer status, discrepancy = 1
        $discrepancy = ($record['worker_attendance'] != $newCustomerStatus) ? 1 : 0;

        // 3. Update the restricted fields
        $updateData = [
            'customer_side_attendance' => $newCustomerStatus,
            'discrepancy'              => $discrepancy
        ];

        if ($attendanceModel->update($data['id'], $updateData)) {
            return $this->respond([
                'status'  => 200,
                'success' => true,
                'message' => ($discrepancy == 1) ? "Marked as Absent. Discrepancy flagged for Admin." : "Attendance verified.",
                'data'    => ['discrepancy' => $discrepancy]
            ]);
        }

        return $this->failServerError("Failed to update status.");
    }

    /**
     * ADMIN VIEW: 
     * Get all attendance records with names
     */
    public function index()
    {
        $attendanceModel = new AttendanceModel();
        return $this->respond([
            'status'  => 200,
            'success' => true,
            'data'    => $attendanceModel->getFullAttendance()
        ]);
    }
}