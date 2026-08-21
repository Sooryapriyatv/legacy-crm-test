<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    public function authenticate()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Simple hardcoded authentication (for demo purposes)
        if ($username === 'admin' && $password === 'admin123') {
            session()->set([
                'user_id' => 1,
                'username' => 'admin',
                'logged_in' => true
            ]);
            return redirect()->to('/dashboard')->with('success', 'Welcome back!');
        }

        return redirect()->back()->with('error', 'Invalid credentials');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Logged out successfully');
    }
}
