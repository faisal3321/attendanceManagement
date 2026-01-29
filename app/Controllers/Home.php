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
}
