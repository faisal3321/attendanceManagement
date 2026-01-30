<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('welcome_message');
    }

    public function register()
    {
        return view('customer/register'); 
    }

    public function login()
    {
        return view('customer/login'); 
    }

    public function dashboard()
    {
        return view('customer/dashboard'); 
    }

    public function booking()
    {
        return view('customer/bookWorker'); 
    }

    public function attendanceCust()
    {
        return view('customer/attendance'); 
    }

    public function myBooking()
    {
        $session = session(); 
        
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('customer/login');
        }
        return view('customer/bookWorker'); 
    }

    // admin view
    public function adminLogin() 
    {
        return view('admin/login');
    }

    public function adminDashboard() 
    {
        return view('admin/dashboard');
    }

    public function adminAddWorker() 
    {
        return view('admin/addWorker');
    }

    public function adminWorkerList() 
    {
        return view('admin/workerList');
    }

    public function workerList()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to(base_url('admin/login'));
        }

        $workerModel = new \App\Models\WorkerModel();
        // Fetch all workers to show both active and inactive
        $data['workers'] = $workerModel->orderBy('created_at', 'DESC')->findAll();

        return view('admin/worker_list', $data);
    }

    public function editWorker($id)
    {
        $model = new \App\Models\WorkerModel();
        $data['worker'] = $model->find($id);

        if (!$data['worker']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('admin/edit_worker', $data);
    }


}
