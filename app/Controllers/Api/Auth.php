<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Libraries\JwtService;
use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        $data = $this->request->getJSON(true);

        if (!is_array($data)) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'status' => false,
                    'message' => 'Invalid JSON request.'
                ]);
        }

        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';

        if ($email === '' || $password === '') {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'status' => false,
                    'message' => 'Email and password are required.'
                ]);
        }

        $userModel = new UserModel();

        $user = $userModel
            ->where('email', $email)
            ->first();

        if (!$user || !password_verify($password, $user['password'])) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'status' => false,
                    'message' => 'Invalid email or password.'
                ]);
        }

        $jwtService = new JwtService();

        $tokenData = $jwtService->generateToken($user);

        return $this->response
            ->setStatusCode(200)
            ->setJSON([
                'status' => true,
                'message' => 'Login successful.',
                'data' => $tokenData
            ]);
    }
}