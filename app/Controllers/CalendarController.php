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
        
        // ADD THIS VALIDATION: Prevent future date generation
        $today = date('Y-m-d');
        if ($startDate > $today) {
            return $this->fail('Cannot generate calendar for future start date', 400);
        }
        
        // Prevent future dates
        $today = date('Y-m-d');
        if ($endDate > $today) {
            return $this->fail('Cannot generate calendar for future dates', 400);
        }
        
        // Prevent generating calendar older than 2 months 
        $twoMonthsAgo = date('Y-m-d', strtotime('-2 months'));
        if ($startDate < $twoMonthsAgo) {
            return $this->fail('Cannot generate calendar older than 2 months', 400);
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
                
                // Sync attendance only for past/current dates
                if ($dateStr <= $today) {
                    $attendanceService->syncDailyAttendance($dateStr);
                }
                $count++;
            }
            
            $current = strtotime('+1 day', $current);
        }
        
        return $this->respond([
            'status'  => 200,
            'success' => true,
            'message' => "Successfully generated $count calendar days."
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