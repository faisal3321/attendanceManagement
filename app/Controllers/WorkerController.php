<?php

namespace App\Controllers;

use App\Models\WorkerModel;
use App\Models\SuperAdminModel;
use CodeIgniter\RESTful\ResourceController;
use App\Services\AttendanceSyncService;

class WorkerController extends ResourceController
{
    protected $format = 'json';

    public function create()
    {
        // Admin authentication
        $adminId = $this->request->getHeaderLine('X-ADMIN-ID');

        if (! $adminId) {
            return $this->failUnauthorized('Admin ID is required');
        }

        $adminModel = new SuperAdminModel();
        if (! $adminModel->find($adminId)) {
            return $this->failUnauthorized('Invalid Admin');
        }

        // Request data
        $data = $this->request->getJSON(true) ?? [];

        $required = ['name', 'age', 'gender', 'phone'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return $this->fail("$field is required", 400);
            }
        }

        $workerModel = new WorkerModel();

        // Prepare data for insertion
        $insertData = [
            'name'       => $data['name'],
            'age'        => (int) $data['age'],
            'gender'     => $data['gender'],
            'phone'      => $data['phone'],
            'address'    => $data['address'] ?? null,
            'created_by' => (int) $adminId,
            'status'     => 'active',
        ];

        try {
            // Insert worker - MySQL will generate the auto-increment ID
            if (! $workerModel->insert($insertData)) {
                return $this->failServerError('Failed to create worker.');
            }

            // Get the auto-generated ID (this is the worker's ID now)
            $workerId = $workerModel->getInsertID();

            // Sync attendance for today using service
            $attendanceService = new AttendanceSyncService();
            $attendanceService->syncDailyAttendance(date('Y-m-d'));

            return $this->respondCreated([
                'status'  => 201,
                'success' => true,
                'message' => 'Worker created successfully!',
                'data'    => [
                    'id'   => $workerId,
                    'name' => $data['name']
                ]
            ]);

        } catch (\Exception $e) {
            return $this->failServerError('Failed to create worker: ' . $e->getMessage());
        }
    }

    // Fetch active workers (booking side)
    public function index()
    {
        $workerModel = new WorkerModel();
        $workers = $workerModel->where('status', 'active')->findAll();

        return $this->respond([
            'status'  => 200,
            'success' => true,
            'data'    => $workers
        ]);
    }

    // Delete worker
    public function delete($id = null)
    {
        $model = new WorkerModel();

        if (! $model->find($id)) {
            return $this->failNotFound('Worker not found');
        }

        if ($model->delete($id)) {
            return $this->respondDeleted([
                'status'  => 200,
                'success' => true,
                'message' => 'Worker deleted successfully'
            ]);
        }

        return $this->failServerError('Could not delete worker');
    }

    // Update worker
    public function update($id = null)
    {
        $model = new WorkerModel();

        if (! $model->find($id)) {
            return $this->failNotFound('Worker not found');
        }

        $data = $this->request->getJSON(true);
        
        if (empty($data)) {
            return $this->fail('No data provided for update', 400);
        }

        $updateData = [];
        
        // Only update fields that are provided
        if (isset($data['name'])) {
            $updateData['name'] = $data['name'];
        }
        
        if (isset($data['age'])) {
            $updateData['age'] = (int) $data['age'];
        }
        
        if (isset($data['phone'])) {
            $updateData['phone'] = $data['phone'];
        }
        
        if (isset($data['address'])) {
            $updateData['address'] = $data['address'];
        }
        
        if (isset($data['status'])) {
            $updateData['status'] = $data['status'];
        }

        if (empty($updateData)) {
            return $this->fail('No valid fields to update', 400);
        }

        if ($model->update($id, $updateData)) {
            return $this->respond([
                'status'  => 200,
                'success' => true,
                'message' => 'Worker updated successfully'
            ]);
        }

        return $this->failServerError('Failed to update worker');
    }
}