<?php

namespace App\Controllers;

use App\Models\WorkerModel;
use App\Models\SuperAdminModel;
use CodeIgniter\RESTful\ResourceController;

class WorkerController extends ResourceController
{
    protected $format = 'json';

    public function create()  
    {
        // authentication check we will change it to token 
        $adminId = $this->request->getHeaderLine('X-ADMIN-ID');

        if (! $adminId) {
            return $this->failUnauthorized('Admin ID is required');
        }

        $adminModel = new SuperAdminModel();
        $admin = $adminModel->find($adminId);

        if (! $admin) {
            return $this->failUnauthorized('Invalid Admin');
        }

        // get JSON request data
        $data = $this->request->getJSON(true) ?? [];

        $requiredField = [
            'name', 
            'age', 
            'gender', 
            'phone'
        ];

        // validation that the field should not be empty
        foreach ($requiredField as $field) {
            if (empty($data[$field])) {
                return $this->failValidationError("$field is required");
            }
        }

        $workerModel = new WorkerModel;

        // generate worker ID
        $timeUnix = time();
        $workerId = 'WRK' . $timeUnix;

        // insert data into db
        $insertData = [
            'worker_id'  => $workerId,
            'name'       => $data['name'],
            'age'        => (int) $data['age'],
            'gender'     => $data['gender'],
            'phone'      => $data['phone'],
            'address'    => $data['address'] ?? null,
            'created_by' => (int) $adminId,
            'status'     => 'active',
        ];

        // insert data
        if (! $workerModel->insert($insertData)) {
            // if it fails, give error message
            return $this->failServerError('Failed to create worker. Please try again in a second.');
        }

        // respond
        return $this->respondCreated([
            'status'  => 201,
            'success' => true,
            'message' => 'Worker created successfully!',
            'data'    => [
                'worker_id' => $workerId
            ]
        ]);
    }

    // for fetching worker at the time of booking
    public function index()
    {
        $workerModel = new WorkerModel();
        
        // Get only active workers
        $workers = $workerModel->where('status', 'active')->findAll();

        if (empty($workers)) {
            return $this->respond([
                'status'  => 200,
                'success' => false,
                'message' => 'No workers found',
                'data'    => []
            ]);
        }

        return $this->respond([
            'status'  => 200,
            'success' => true,
            'message' => 'Workers retrieved successfully',
            'data'    => $workers
        ]);
    }
    
}