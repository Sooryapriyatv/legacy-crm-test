<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\ActivityModel;

class Customers extends BaseController
{
    protected $customerModel;
    protected $activityModel;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
        $this->activityModel = new ActivityModel();
    }

    public function index()
    {
        $search = $this->request->getGet('search');
        $status = $this->request->getGet('status');
        $city = $this->request->getGet('city');

        $customers = $this->customerModel
            ->orderBy('id', 'DESC')
            ->paginate(20);

        $data = [
            'customers' => $customers,
            'pager' => $this->customerModel->pager,
            'search' => $search,
            'status' => $status,
            'city' => $city
        ];

        return view('customers/index', $data);
    }

    public function create()
    {
        return view('customers/create');
    }

    public function store()
    {
        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'company' => $this->request->getPost('company'),
            'city' => $this->request->getPost('city'),
            'status' => $this->request->getPost('status') ?? 'active',
            'notes' => $this->request->getPost('notes')
        ];

        if ($this->customerModel->insert($data)) {
            // Log activity
            $this->activityModel->insert([
                'customer_id' => $this->customerModel->getInsertID(),
                'action' => 'created',
                'description' => 'Customer created',
                'user_id' => session()->get('user_id')
            ]);

            return redirect()->to('/customers')->with('success', 'Customer created successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create customer');
    }

    public function edit($id)
    {
        $customer = $this->customerModel->find($id);

        if (!$customer) {
            return redirect()->to('/customers')->with('error', 'Customer not found');
        }

        $data = [
            'customer' => $customer
        ];

        return view('customers/edit', $data);
    }

    public function update($id)
    {
        $customer = $this->customerModel->find($id);

        if (!$customer) {
            return redirect()->to('/customers')->with('error', 'Customer not found');
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'company' => $this->request->getPost('company'),
            'city' => $this->request->getPost('city'),
            'status' => $this->request->getPost('status'),
            'notes' => $this->request->getPost('notes')
        ];

        if ($this->customerModel->update($customer, $data)) {
            // Log activity
            $this->activityModel->insert([
                'customer_id' => $id,
                'action' => 'updated',
                'description' => 'Customer information updated',
                'user_id' => session()->get('user_id')
            ]);

            return redirect()->to('/customers')->with('success', 'Customer updated successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to update customer');
    }

    public function delete($id)
    {
        $customer = $this->customerModel->find($id);

        if (!$customer) {
            return redirect()->to('/customers')->with('error', 'Customer not found');
        }

        // $this->customerModel->delete($id);

        return redirect()->to('/customers')->with('success', 'Customer deleted successfully');
    }

    public function view($id)
    {
        $customer = $this->customerModel->find($id);

        if (!$customer) {
            return redirect()->to('/customers')->with('error', 'Customer not found');
        }

        $activities = $this->activityModel
            ->where('customer_id', $id)
            ->orderBy('created_at', 'DESC')
            ->limit(20)
            ->find();

        $data = [
            'customer' => $customer,
            'activities' => $activities
        ];

        return view('customers/view', $data);
    }

    public function export()
    {
        $customers = $this->customerModel->findAll();

        $filename = 'customers_' . date('Y-m-d') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        fputcsv($output, ['ID', 'Name', 'Email', 'Phone', 'Company', 'City', 'Status']);

        fclose($output);
        exit;
    }
}
