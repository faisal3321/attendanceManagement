<?php

namespace App\Controllers;

class ViewController extends BaseController
{
    public function home()
    {
        return view('home');
    }
    
    public function customerRegister()
    {
        return view('customer/register');
    }
    
    public function customerLogin()
    {
        return view('customer/login');
    }
    
    public function customerDashboard()
    {
        if(!session()->get('customer_id')) {
            return redirect()->to('/customer/login');
        }
        
        $data = [
            'customer_id' => session()->get('customer_id'),
            'customer_name' => session()->get('customer_name')
        ];
        
        return view('customer/dashboard', $data);
    }
    
    public function bookWorker()
    {
        if(!session()->get('customer_id')) {
            return redirect()->to('/customer/login');
        }
        return view('customer/book_worker');
    }
    
    public function markAttendance()
    {
        if(!session()->get('customer_id')) {
            return redirect()->to('/customer/login');
        }
        return view('customer/mark_attendance');
    }
    
    public function customerLogout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
    
    public function adminLogin()
    {
        return view('admin/login');
    }
    
    public function adminDashboard()
    {
        if(!session()->get('admin_id')) {
            return redirect()->to('/admin/login');
        }
        return view('admin/dashboard');
    }
    
    public function addWorker()
    {
        if(!session()->get('admin_id')) {
            return redirect()->to('/admin/login');
        }
        return view('admin/add_worker');
    }
    
    public function manageAttendance()
    {
        if(!session()->get('admin_id')) {
            return redirect()->to('/admin/login');
        }
        return view('admin/manage_attendance');
    }
    
    public function manageCustomers()
    {
        if(!session()->get('admin_id')) {
            return redirect()->to('/admin/login');
        }
        return view('admin/manage_customers');
    }
    
    public function adminLogout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
    
    public function setSession()
    {
        if ($this->request->getMethod() === 'post') {
            $customer_id = $this->request->getPost('customer_id');
            $customer_name = $this->request->getPost('customer_name');
            
            if ($customer_id && $customer_name) {
                session()->set([
                    'customer_id' => $customer_id,
                    'customer_name' => $customer_name
                ]);
                return redirect()->to('/customer/dashboard');
            }
            
            $admin_id = $this->request->getPost('admin_id');
            $is_super = $this->request->getPost('is_super');
            
            if ($admin_id) {
                session()->set([
                    'admin_id' => $admin_id,
                    'is_super' => $is_super
                ]);
                return redirect()->to('/admin/dashboard');
            }
        }
        
        return redirect()->to('/');
    }
}