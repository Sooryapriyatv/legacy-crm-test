<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomerModel;

class Dashboard extends BaseController
{
    public function accessDenied()
    {
        return view('errors/access_denied');
    }

    public function index()
    {
        $customerModel = new CustomerModel();

        $totalCustomers = $customerModel->countAllResults();
        $activeCustomers = $customerModel->where('status', 'active')->countAllResults();    
        $recentCustomers = $customerModel->orderBy('created_at', 'DESC')->limit(5)->findAll();
        $data = [
            'total_customers' => $totalCustomers,
            'active_customers' => $activeCustomers,
            'recent_customers' => $recentCustomers
        ];

        return view('dashboard/index', $data);
    }
}
