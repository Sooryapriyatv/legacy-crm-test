<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleCheck implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (!$session->get('logged_in')) {
            return redirect()->to('/login');
        }

        $role = $session->get('role');

        if (!$role) {
            return redirect()->to('/access-denied');
        }

        // No specific role restriction
        if (empty($arguments)) {
            return;
        }

        if (!in_array($role, $arguments)) {
            return redirect()->to('/access-denied');
        }
    }

    public function after(
        RequestInterface $request,
        ResponseInterface $response,
        $arguments = null
    ) {
        // Nothing required here
    }
}