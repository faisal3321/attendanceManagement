<?php

namespace App\Controllers;

use App\Models\WorkerModel;
use App\Models\SuperAdminModel;
use CodeIgniter\RESTful\ResourceController;

class WorkerController extends ResourceController
{
    protected $format = 'json';

    // POST: api/add-worker (BY admin or super-admin)
    public function addWorker()  
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

    }
}
