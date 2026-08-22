<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Auth extends BaseController
{
    protected $userModel;

    public function __construct()
    {
       $this->userModel = new UserModel();
    }

    public function login()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    public function authenticate()
    {
        $username = trim((string) $this->request->getPost('username'));
        $password = $this->request->getPost('password');

        if ($username === '' || !is_string($password) || $password === '') {
            return redirect()->back()->withInput()->with('error', 'Invalid credentials');
        }

        $user = $this->userModel
            ->where('email', $username)
            ->first();

        if (!$user) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Invalid credentials');
        }

        // Simple hardcoded authentication (for demo purposes)
        if (!password_verify($password, $user['password'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Invalid credentials');
        }

        session()->regenerate(true);
        session()->set([
            'user_id'   => $user['id'],
            'username'  => $user['name'],
            'role'      => $user['role'],
            'logged_in' => true,
        ]);
            return redirect()->to('/dashboard')->with('success', 'Welcome back!');


    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Logged out successfully');
    }
}
