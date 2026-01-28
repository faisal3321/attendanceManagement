<?php

namespace App\Controllers;

use App\Models\CustomerModel;
use CodeIgniter\RESTful\ResourceController;

class CustomerController extends ResourceController
{
    protected $format = 'json';

    // POST: /api/customer/register
    public function register()
    {
        $model = new CustomerModel();
        $data = $this->request->getJSON(true) ?? [];

        // 1. Check if all 4 required fields are present and not empty
        $required = ['name', 'email', 'password', 'phone'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return $this->failValidationError("$field is required");
            }
        }

        // 2. Hash the password
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

        // 3. Insert into database
        if ($model->insert($data)) {
            return $this->respondCreated([
                'status'  => 201,
                'success' => true,
                'message' => 'Customer registered successfully!'
            ]);
        }

        return $this->fail($model->errors());
    }

    // POST:  /api/customer/login
    public function login()
    {
        $model = new CustomerModel();
        $data = $this->request->getJSON(true) ?? [];

        // We need (email or phone number) and password
        $username = $data['username'] ?? ''; // This can be email or phone
        $password = $data['password'] ?? '';

        if (empty($username) || empty($password)) {
            return $this->failValidationError('Email/Phone and Password are required');
        }

        // check for email or phone number 
        $customer = $model->groupStart()
                      ->where('email', $username)
                      ->orWhere('phone', $username)
                  ->groupEnd()
                  ->first();

        if (!$customer) {
            return $this->failNotFound('User not found');
        }

        // checking if the user password matched with hashed password
        if (!password_verify($password, $customer['password'])) {
            return $this->failUnauthorized('Invalid password');
        }

        // // Remove password from response for safety
        // unset($user['password']);

        return $this->respond([
            'status'  => 200,
            'message' => 'Login successful',
            'data'    => $user
        ]);
    }
}