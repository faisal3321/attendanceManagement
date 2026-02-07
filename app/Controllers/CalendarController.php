<?php

namespace App\Controllers;

use App\Models\CalendarModel;
use App\Models\SuperAdminModel; // Added this
use App\Controllers\BaseController;
use App\Controllers\AttendanceController;
use CodeIgniter\API\ResponseTrait; // Added this

class CalendarController extends BaseController
{
    // Now the trait will be found correctly
    use ResponseTrait;

    public function generateRange()
    {
        // Authentication Check
        $adminId = $this->request->getHeaderLine('X-ADMIN-ID');
        if (!$adminId) {
            return $this->failUnauthorized('Admin ID is required');
        }

        $adminModel = new SuperAdminModel();
        if (!$adminModel->find($adminId)) {
            return $this->failUnauthorized('Invalid Admin Access');
        }

        // Get Input Data
        $data = $this->request->getJSON(true);

        $startDate = $data['start_date'] ?? null; // format: YYYY-MM-DD
        $endDate   = $data['end_date'] ?? null;   // format: YYYY-MM-DD

        if (!$startDate || !$endDate) {
            return $this->failValidationError('Start and End dates are required');
        }

        $model = new CalendarModel();
        $today = date('Y-m-d');

        if ($endDate > $today) {
            return $this->failValidationError('Future dates are not allowed.');
        }

        if ($startDate > $endDate) {
            return $this->failValidationError('Start date cannot be after end date.');
        }
        
        // The Generation Loop
        $current = strtotime($startDate);
        $last    = strtotime($endDate);
        $count   = 0;

        // Inside CalendarController.php -> generateRange() loop:

        while ($current <= $last) {
            $dateStr = date('Y-m-d', $current);
            $exists = $model->where('calendar_date', $dateStr)->first();

            if (!$exists) {
                $dayName = date('l', $current);
                $newData = [
                    'calendar_id'   => 'CAL' . date('Ymd', $current),
                    'calendar_date' => $dateStr,
                    'day'           => $dayName,
                    'month'         => date('F', $current),
                    'year'          => date('Y', $current),
                    'is_weekend'    => ($dayName === 'Sunday') ? 1 : 0,
                ];
                $model->insert($newData);
                $count++;

                // FIX PROBLEM 2: Pass the specific $dateStr to the sync function
                // This creates attendance records for EVERY day generated
                $att = new AttendanceController();
                $att->syncDailyAttendance($dateStr); 
            }
            $current = strtotime('+1 day', $current);
        }

        return $this->respond([
            'status'  => 200,
            'success' => true,
            'message' => "Successfully generated $count new days. Future dates were synced with attendance.",
        ]);
    }

    public function index()
    {
        $model = new CalendarModel();

        $todayDate = date('Y-m-d');
        $currentDayName = date('l');

        // check if today date already exist in calendar
        $todayDateExists = $model->where('calendar_date', $todayDate)->first();

        if (! $todayDateExists) {
            $isWeekend = ($currentDayName === 'Sunday') ? 1 : 0;

            $newData = [
                'calendar_id'   => 'CAL' . date('Ymd'),
                'calendar_date' => $todayDate,
                'day'           => $currentDayName,
                'month'         => date('F'),
                'year'          => date('Y'),
                'is_weekend'    => $isWeekend,
            ];

            $model->insert($newData);

            $att = new AttendanceController();
            $att->syncDailyAttendance();
        }

        $allData = $model->orderBy('calendar_date', 'DESC')->findAll();

        return $this->response->setJSON($allData);
    }
}