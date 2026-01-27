<?php

namespace App\Controllers;

use App\Models\WorkerModel;
use App\Models\SuperAdminModel;
use CodeIgniter\RESTful\ResourceController;

class WorkerController extends ResourceController
{
    protected $format = 'json';

    // POST: api/add-worker (BY admin or super-admin)
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

        $requiredField = ['name', 'age', 'gender', 'phone'];

        foreach ($requiredField as $field) {
            if (empty($data[$field])) {
                return $this->failValidationError("$field is required");
            }
        }

        // generate unique worker_id
        $workerModel = new WorkerModel;
        $workerId = $this->generateWorkerId($workerModel);

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
            return $this->failServerError('failed to create worker');
        }

        // respond
        return $this->respondCreated([
            'status'        => 201,
            'message'       => 'Worker created successfully!',
            'data'          => [
                'worker_id'          => $workerId
            ]
        ]);
    }

    // function for generate unique worker ID
        private function generateWorkerId(WorkerModel $workerModel) {
            do {
                $workerId = 'WRK' . str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT);
            }
            while ($workerModel->where('worker_id', $workerId)->first());

            return $workerId;
        }
}
