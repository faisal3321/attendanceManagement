<?php

namespace App\Controllers;

use App\Models\AttendanceModel;
use App\Models\BookingModel;
use App\Models\CalendarModel;
use CodeIgniter\RESTful\ResourceController;

class AttendanceController extends ResourceController
{
    protected $format = 'json';

    
    // This takes the date from the Calendar and creates attendance for all active bookings.
    
    public function syncDailyAttendance()
    {
        $attendanceModel = new AttendanceModel();
        $bookingModel    = new BookingModel();
        $calendarModel   = new CalendarModel();

        // first know today date from calendar
        $today = date('Y-m-d');
        $calendarDate = $calendarModel->where('calendar_date', $today)->first();

        if (!$calendarDate) {
            return $this->fail("Calendar date for today not found. Please hit calendar api first.");
        }

        $targetDate = $calendarDate['calendar_date'];

        // know all the booking to generate daily attendance
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

    
    // customer can only change customer side attendance
    public function updateCustomerStatus()
    {
        $attendanceModel = new AttendanceModel();
        $data = $this->request->getJSON(true);

        if (empty($data['id'])) {
            return $this->failValidationError("Attendance ID is required.");
        }

        // fetch the current record
        $record = $attendanceModel->find($data['id']);
        if (!$record) return $this->failNotFound("Attendance record not found.");

        $newCustomerStatus = (int)$data['customer_side_attendance']; // Expects 0 or 1

        // if admin_side and customer_side attendance does not match, show discrepancy = 1 otherwise discrepancy = 0
        $discrepancy = ($record['worker_attendance'] != $newCustomerStatus) ? 1 : 0;

        // update the restricted fields
        $updateData = [
            'customer_side_attendance' => $newCustomerStatus,
            'discrepancy'              => $discrepancy
        ];

        if ($attendanceModel->update($data['id'], $updateData)) {
            return $this->respond([
                'status'  => 200,
                'success' => true,
                'message' => ($discrepancy == 1) ? "Marked as Absent" : "Attendance verified",
                'data'    => ['discrepancy' => $discrepancy]
            ]);
        }

        return $this->failServerError("Failed to update status.");
    }

    // Get all attendance record
    public function index()
    {
        $attendanceModel = new AttendanceModel();
        return $this->respond([
            'status'  => 200,
            'success' => true,
            'data'    => $attendanceModel->getFullAttendance()
        ]);
    }


    // admin can manually change admin_side and customer_side attendance as well as punch time
    public function adminOverride()
    {
        $attendanceModel = new AttendanceModel();
        $data = $this->request->getJSON(true);

        if (empty($data['id'])) {
            return $this->failValidationError("Attendance ID is required for override.");
        }

        // fetch current record
        $record = $attendanceModel->find($data['id']);
        if (!$record) return $this->failNotFound("Attendance record not found.");

        // prepare data update (uses existing values if fields are missing in JSON)
        $w_att = isset($data['worker_attendance']) ? (int)$data['worker_attendance'] : (int)$record['worker_attendance'];
        $c_att = isset($data['customer_side_attendance']) ? (int)$data['customer_side_attendance'] : (int)$record['customer_side_attendance'];
        
        //  discrepancy get resolved automatically if the attendance get matched
        $discrepancy = ($w_att != $c_att) ? 1 : 0;

        $updateData = [
            'worker_attendance'        => $w_att,
            'customer_side_attendance' => $c_att,
            'discrepancy'              => $discrepancy
        ];

        if ($attendanceModel->update($data['id'], $updateData)) {
            return $this->respond([
                'status'  => 200,
                'success' => true,
                'message' => "Admin Override successful. Record updated.",
                'data'    => $updateData
            ]);
        }

        return $this->failServerError("Failed to perform override.");
    }

    // GET: /api/attendance/customer/:customerId
    public function showByCustomer($customerId = null)
    {
        $attendanceModel = new AttendanceModel();
        // Assuming your model has a way to join with worker names
        $data = $attendanceModel->select('attendance.*, workers.name as worker_name')
                ->join('workers', 'workers.worker_id = attendance.worker_id')
                ->where('attendance.customer_id', $customerId)
                ->orderBy('attendance_date', 'DESC')
                ->findAll();

        return $this->respond([
            'status'  => 200,
            'success' => true,
            'data'    => $data
        ]);
    }
    
}