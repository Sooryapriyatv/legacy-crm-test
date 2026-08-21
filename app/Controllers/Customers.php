<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\ActivityModel;
use App\Services\EmailService;

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
       $rules = [
    'search' => [
        'rules'  => 'permit_empty|regex_match[/^[a-zA-Z0-9@.\s]*$/]|max_length[100]',
        'errors' => [
            'regex_match' => 'Search field can contain only letters, numbers, and spaces.'
        ]
    ],
    'city' => [
        'rules'  => 'permit_empty|regex_match[/^[a-zA-Z\s]*$/]|max_length[50]',
        'errors' => [
            'regex_match' => 'City can contain only letters and spaces.'
        ]
    ],
    'status' => [
        'rules' => 'permit_empty|in_list[active,inactive,pending]'
    ]
];

if (! $this->validate($rules)) {
    return redirect()
        ->to(base_url('customers'))
        ->withInput()
        ->with('errors', $this->validator->getErrors());
}


        $search = $this->request->getGet('search');
        $status = $this->request->getGet('status');
        $city = $this->request->getGet('city');

        $builder = $this->customerModel;

        if(!empty($search)) {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('email', $search)
                ->groupEnd();
        }

        if(!empty($status)) {
            $builder->where('status', $status);
        }

        if(!empty($city)) {
            $builder->where('city', $city);
        }

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

        $rules = [
        'name'    => 'required|min_length[2]|max_length[100]|regex_match[/^[a-zA-Z\s]+$/]',
        'email' => 'required|valid_email|max_length[150]|is_unique[customers.email]',
        'phone'   => 'permit_empty|regex_match[/^[0-9]+$/]|max_length[20]',
        'company' => 'permit_empty|max_length[150]',
        'city'    => 'permit_empty|max_length[100]|regex_match[/^[a-zA-Z\s]+$/]',
        'status'  => 'required|in_list[active,inactive,pending]',
        'notes'   => 'permit_empty|max_length[1000]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

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
                'user_id' => session()->get('user_id'),
                'created_at'  => date('Y-m-d H:i:s')
            ]);

            // Email failure will NOT break customer creation.
    $emailService = new EmailService();
    $emailService->sendWelcomeEmail($data);

            return redirect()->to('/customers')->with('success', 'Customer created successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create customer');
    }

    public function edit($id)
    {
        if (!is_numeric($id)) {
            return redirect()->to('/customers')->with('error', 'Invalid customer ID');
        }

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
        $rules = [
    'name'    => 'required|min_length[2]|max_length[100]|regex_match[/^[a-zA-Z\s]+$/]',
    'email' => 'required|valid_email|max_length[150]|is_unique[customers.email,id,' . $id . ']',
    'phone'   => 'permit_empty|regex_match[/^[0-9]+$/]|max_length[20]',
    'company' => 'permit_empty|max_length[150]',
    'city'    => 'permit_empty|max_length[100]|regex_match[/^[a-zA-Z\s]+$/]',
    'status'  => 'required|in_list[active,inactive,pending]',
    'notes'   => 'permit_empty|max_length[1000]',
];
        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }


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
                'user_id' => session()->get('user_id'),
                'created_at' => date('Y-m-d H:i:s')
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

        $this->customerModel->delete($id);

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

        // CSV Data
        foreach ($customers as $customer) {
            fputcsv($output, [
                $customer['id'],
                $customer['name'],
                $customer['email'],
                $customer['phone'],
                $customer['company'],
                $customer['city'],
                $customer['status']
            ]);
        }

        fclose($output);
        exit;
    }
}
