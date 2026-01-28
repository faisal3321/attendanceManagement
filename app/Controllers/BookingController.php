<?php

namespace App\Controllers;

use App\Models\BookingModel;
use App\Models\WorkerModel;
use CodeIgniter\RESTful\ResourceController;

class BookingController extends ResourceController
{
    protected $format = 'json';

    // GET: /api/bookings
    public function index()
    {
        $model = new BookingModel();
        $data = $model->getFullDetails();

        return $this->respond([
            'status'   => 200,
            'success'  => true,
            'message'  => !empty($data) ? 'All bookings are shown below.' : 'No bookings found in the system.',
            'data'     => $data
        ]);
    }


    // POST: /api/book-worker
    public function create()
    {
        $model = new BookingModel();
        $data = $this->request->getJSON(true) ?? [];

        // Validation
        $required = ['customer_id', 'worker_id', 'duration_months'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return $this->failValidationError("$field is missing. Please provide all required information.");
            }
        }

        // Verify Worker
        $workerModel = new WorkerModel();
        $worker = $workerModel->where('worker_id', $data['worker_id'])->first();
        
        if (!$worker) {
            return $this->failNotFound("The worker does not exist.");
        }

        // generate booking id 
        $bookingId = 'BK' . time();
        
        $insertData = [
            'booking_id'      => $bookingId,
            'customer_id'     => $data['customer_id'],
            'worker_id'       => $data['worker_id'],
            'duration_months' => (string) $data['duration_months'],
        ];

        if ($model->insert($insertData)) {
            return $this->respondCreated([
                'status'  => 201,
                'success' => true,
                'message' => "Congratulations! Worker is ready to serve you...",
                'data'    => [
                    'booking_id'  => $bookingId,
                    'worker_name' => $worker['name'],
                    'duration'    => $data['duration_months'] . ' Months'
                ]
            ]);
        }

        return $this->failServerError('Something went wrong. Please try again.');
    }

    

    // GET: /api/my-bookings/:customerId
    public function show($customerId = null)
    {
        $model = new BookingModel();
        
        $bookings = $model->select('bookings.*, workers.name as worker_name')
                        ->join('workers', 'workers.worker_id = bookings.worker_id')
                        ->where('bookings.customer_id', $customerId)
                        ->findAll();

        if (empty($bookings)) {
            return $this->respond([
                'status'  => 200,
                'success' => true,
                'message' => "No active bookings found for Customer ID: $customerId",
                'data'    => []
            ]);
        }

        return $this->respond([
            'status'  => 200,
            'success' => true,
            'message' => "Successfully get the bookings",
            'data'    => $bookings
        ]);
    }
}