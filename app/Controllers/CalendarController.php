<?php

namespace App\Controllers;

use App\Models\CalendarModel;
use App\Controllers\BaseController;

class CalendarController extends BaseController
{
    public function index()
    {
        $model = new CalendarModel();

        $todayDate = date('Y-m-d');
        $currentDayName = date('l');

        // check if today date already exist in calendar
        $todayDateExists = $model->where('calendar_date', $todayDate)->first();

        if (! $todayDateExists) {

            // check if today is weekend or not
            $is_weekend = 0;
            if($currentDayName === 'Sunday') {
                $is_weekend = 1;
            }

            $newData = [
                'calendar_id'   => 'CAL-' . date('Ymd'),
                'calendar_date' => $todayDate,
                'day'           => $currentDayName,
                'month'         => date('F'),
                'year'          => date('Y'),
                'is_weekend'    => $isWeekend,
            ];

            $model->insert($newData);

        }

        // show all latest date at the top 
        $allData = $model->orderBy('calendar_date', 'DESC')->findAll();

        return $this->response->setJSON($allData);
    }
}
