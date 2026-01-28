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
        // Auth check
        $adminId = $this->request->getHeaderLine('X-ADMIN-ID');

        if (! $adminId) {
            return $this->failUnauthorized('Admin ID is required');
        }

        $adminModel = new SuperAdminModel();
        $admin = $adminModel->find($adminId);

        if (! $admin) {
            return $this->failUnauthorized('Invalid Admin');
        }

        // Get request data
        $data = $this->request->getJSON(true) ?? [];

        $requiredField = [
            'name', 
            'age', 
            'gender', 
            'phone'
        ];

        foreach ($requiredField as $field) {
            if (empty($data[$field])) {
                return $this->failValidationError("$field is required");
            }
        }

        $workerModel = new WorkerModel;

        // Logic to generate worker ID
        $workerId = 'WRK' . date('His');

        // prepare insert data
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
            // If it fails, it's likely because the ID already exists (same second)
            return $this->failServerError('Failed to create worker. Please try again in a second.');
        }

        // respond
        return $this->respondCreated([
            'status'  => 201,
            'message' => 'Worker created successfully!',
            'data'    => [
                'worker_id' => $workerId
            ]
        ]);
    }
}