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
                return $this->failValidationError("$field is required");
            }
        }

        $workerModel = new WorkerModel();

        // Generate worker ID
        $workerId = 'WRK' . time() . rand(100, 999);

        // Insert worker
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

        if (! $workerModel->insert($insertData)) {
            return $this->failServerError('Failed to create worker.');
        }

        /**
         * ✅ IMPORTANT FIX
         * Do NOT manually insert attendance.
         * Always delegate to AttendanceController.
         */
        $attendanceController = new \App\Controllers\AttendanceController();
        $attendanceController->syncDailyAttendance(date('Y-m-d'));

        return $this->respondCreated([
            'status'  => 201,
            'success' => true,
            'message' => 'Worker created successfully!',
            'data'    => [
                'worker_id' => $workerId
            ]
        ]);
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

        $updateData = [
            'name'    => $data['name'],
            'age'     => (int) $data['age'],
            'phone'   => $data['phone'],
            'address' => $data['address'] ?? null,
        ];

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
