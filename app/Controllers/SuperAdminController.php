<?php

namespace App\Controllers;

use App\Models\SuperAdminModel;
use CodeIgniter\RESTful\ResourceController;

class SuperAdminController extends ResourceController
{
    protected $format = 'json';

    // POST: /api/setup-super-admin (only on time, for the first time)
    public function setupSuperAdmin()
    {
        $model = new SuperAdminModel();

        if ($model->where('is_super_admin', 1)->first()) {
            return $this->failForbidden('Super admin already exists');
        }

        $data = $this->request->getJSON(true) ?? [];

        if (empty($data['username']) || empty($data['password'])) {
            return $this->failValidationError('Username and password required');
        }

        $model->insert([
            'username'       => $data['username'],
            'password'       => password_hash($data['password'], PASSWORD_DEFAULT),
            'is_super_admin' => 1,
        ]);

        return $this->respondCreated([
            'status'  => 201,
            'success' => true,
            'message' => 'Super admin created successfully',
        ]);
    }

    // POST: /api/admin (only super admin can make admin, then admin can use username and password to login into their account)
    public function createAdmin()
    {
        $model = new SuperAdminModel();

        // Checking super admin
        $superUsername = $this->request->getHeaderLine('X-SUPER-USERNAME');
        $superPassword = $this->request->getHeaderLine('X-SUPER-PASSWORD');

        $superAdmin = $model
            ->where('username', $superUsername)
            ->where('is_super_admin', 1)
            ->first();

        if (! $superAdmin || ! password_verify($superPassword, $superAdmin['password'])) {
            return $this->failUnauthorized('Only super admin can create admins');
        }

        $data = $this->request->getJSON(true) ?? [];

        if (empty($data['username']) || empty($data['password'])) {
            return $this->failValidationError('Username and password required');
        }

        //  duplicate check
        if ($model->where('username', $data['username'])->first()) {
            return $this->fail('Admin already exists');
        }

        $model->insert([
            'username'       => $data['username'],
            'password'       => password_hash($data['password'], PASSWORD_DEFAULT),
            'is_super_admin' => 0,
        ]);

        return $this->respondCreated([
            'status'  => 201,
            'success' => true,
            'message' => 'Admin created successfully',
        ]);
    }

    // POST: /api/login
    public function login()
    {
        $data = $this->request->getJSON(true) ?? [];

        if (empty($data['username']) || empty($data['password'])) {
            return $this->failUnauthorized('Invalid credentials');
        }

        $model = new SuperAdminModel();

        $admin = $model->where('username', $data['username'])->first();

        if (! $admin || ! password_verify($data['password'], $admin['password'])) {
            return $this->failUnauthorized('Invalid credentials');
        }

        session()->set([
            'admin_id'   => $admin['id'],
            'admin_name' => $admin['username'],
            'is_super'   => (bool) $admin['is_super_admin'],
            'logged_in'  => true
        ]);

        return $this->respond([
            'status'  => 200,
            'success' => true,
            'message' => 'Login successful',
            'data'    => [
                'admin_id' => $admin['id'],
                'is_super' => (bool) $admin['is_super_admin'],
            ],
        ]);
    }
}
