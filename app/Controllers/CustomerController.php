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
        
        // Get JSON data 
        $data = $this->request->getJSON(true) ?? [];

        // Validation: all 5 field are required
        $requiredFields = [
            'name', 
            'email', 
            'password', 
            'phone', 
            'address'
        ];

        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return $this->failValidationError("$field is required");
            }
        }

        // customer id generation
        $timePart = time();
        $data['customer_id'] = 'CUST' . $timePart;

        // hashing password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        // inserting data into database 
        if ($model->insert($data)) {
            return $this->respondCreated([
                'status'   => 201,
                'success'  => true,
                'message'  => 'Customer registered successfully!',
                'data'     => [
                    'customer_id' => $data['customer_id'],
                    'name'        => $data['name'],
                    'email'       => $data['email']
                ]
            ]);
        }

        // error if something goes wrong
        return $this->fail($model->errors());
    }


    // POST: /api/customer/login
    public function login()
    {
        $model = new CustomerModel();
        $data = $this->request->getJSON(true) ?? [];

        // email and password for login of customer
        $email = $data['email'] ?? null; 
        $password = $data['password'] ?? null;

        if (empty($email) || empty($password)) {
            return $this->failValidationError('Email/Phone and Password are required');
        }

        // finsing the user by email
        $customer = $model->groupStart()
                        ->where('email', $email)
                    ->groupEnd()
                    ->first();

        if (!$customer) {
            return $this->failNotFound('User with this email or phone is not found. Please register first !');
        }

        // verifying the user password with the hashed password
        if (!password_verify($password, $customer['password'])) {
            return $this->failUnauthorized('Password is incorrect. Please provide the right password');
        }

        // START SESSION AND SAVE ID
        $session = session();
        $session->set('customer_id', $customer['customer_id']);
        $session->set('customer_name', $customer['name']);
        $session->set('isLoggedIn', true);

        // // remove password from the object before returning to user
        // unset($user['password']);

        return $this->respond([
            'status'  => 200,
            'success' => true,
            'message' => 'Customer logged In Successfully!!!',
            'data'    => $customer
        ]);
    }
}