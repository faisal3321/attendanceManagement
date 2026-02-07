<?php

namespace App\Controllers;

use App\Models\CalendarModel;
use App\Models\SuperAdminModel;
use App\Services\AttendanceSyncService;
use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;

class CalendarController extends BaseController
{
    use ResponseTrait;

    public function generateRange()
    {
        // Admin authentication
        $adminId = $this->request->getHeaderLine('X-ADMIN-ID');
        
        if (! $adminId) {
            return $this->failUnauthorized('Admin ID is required');
        }
        
        $adminModel = new SuperAdminModel();
        if (! $adminModel->find($adminId)) {
            return $this->failUnauthorized('Invalid Admin Access');
        }
        
        $data = $this->request->getJSON(true);
        
        $startDate = $data['start_date'] ?? null;
        $endDate   = $data['end_date'] ?? null;
        
        if (! $startDate || ! $endDate) {
            return $this->fail('Start and End dates are required', 400);
        }
        
        if ($startDate > $endDate) {
            return $this->fail('Start date cannot be after end date', 400);
        }
        
        $calendarModel = new CalendarModel();
        $attendanceService = new AttendanceSyncService();
        
        $current = strtotime($startDate);
        $last    = strtotime($endDate);
        $count   = 0;
        
        while ($current <= $last) {
            $dateStr = date('Y-m-d', $current);
            
            $exists = $calendarModel
                ->where('calendar_date', $dateStr)
                ->first();
                
            if (! $exists) {
                $dayName = date('l', $current);
                
                $calendarModel->insert([
                    'calendar_date' => $dateStr,
                    'day'           => $dayName,
                    'month'         => date('F', $current),
                    'year'          => date('Y', $current),
                    'is_weekend'    => ($dayName === 'Sunday') ? 1 : 0,
                ]);
                
                // Sync attendance for this specific date
                $attendanceService->syncDailyAttendance($dateStr);
                $count++;
            }
            
            $current = strtotime('+1 day', $current);
        }
        
        return $this->respond([
            'status'  => 200,
            'success' => true,
            'message' => "Successfully generated $count calendar days and synced attendance."
        ]);
    }

    public function index()
    {
        $model = new CalendarModel();
        $today = date('Y-m-d');
        
        // Ensure today exists
        if (! $model->where('calendar_date', $today)->first()) {
            
            $dayName = date('l');
            
            $model->insert([
                'calendar_date' => $today,
                'day'           => $dayName,
                'month'         => date('F'),
                'year'          => date('Y'),
                'is_weekend'    => ($dayName === 'Sunday') ? 1 : 0,
            ]);
            
            $attendanceService = new AttendanceSyncService();
            $attendanceService->syncDailyAttendance($today);
        }
        
        return $this->respond([
            'status' => 200,
            'success' => true,
            'data' => $model->orderBy('calendar_date', 'DESC')->findAll()
        ]);
    }
}