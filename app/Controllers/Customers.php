<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\ActivityModel;
use App\Models\UserModel;
use App\Services\EmailService;

class Customers extends BaseController
{
    protected $customerModel;
    protected $activityModel;
    protected $userModel;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
        $this->activityModel = new ActivityModel();
        $this->userModel = new UserModel();
    }

    protected function canEdit(array $customer): bool
    {
        $role = session()->get('role');
        $userId = (int) session()->get('user_id');

        if ($role === 'admin') {
            return true;
        }

        if ($role === 'sales') {
            return (int) ($customer['assigned_to'] ?? 0) === $userId;
        }

        if ($role === 'manager') {
            return $this->userModel
                ->where('id', $customer['assigned_to'] ?? 0)
                ->where('manager_id', $userId)
                ->first() !== null;
        }

        return false;
    }

    protected function canView(array $customer): bool
    {
        $role = session()->get('role');

        return $role === 'admin'
            || $role === 'manager'
            || $this->canEdit($customer);
    }

    protected function denyAccess()
    {
        return redirect()->to('/access-denied');
    }

public function index()
{
    $rules = [
        'search' => [
            'rules'  => 'permit_empty|regex_match[/^[a-zA-Z0-9@.\s]*$/]|max_length[100]',
        ],
        'city' => [
            'rules'  => 'permit_empty|regex_match[/^[a-zA-Z\s]*$/]|max_length[50]',
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
    $city   = $this->request->getGet('city');

    $role   = session()->get('role');
    $userId = session()->get('user_id');

    $builder = $this->customerModel;

    /*
    |--------------------------------------------------------------------------
    | Role Filtering
    |--------------------------------------------------------------------------
    */
    if ($role === 'sales') {
        $builder->where('assigned_to', $userId);
    } elseif ($role !== 'manager' && $role !== 'admin') {
        $builder->where('id', 0);
    }

    // Manager can see all customers
    // Admin can see all customers

    /*
    |--------------------------------------------------------------------------
    | Search Filters
    |--------------------------------------------------------------------------
    */
    if (!empty($search)) {
        $builder->groupStart()
            ->like('name', $search)
            ->orLike('email', $search)
            ->groupEnd();
    }

    if (!empty($status)) {
        $builder->where('status', $status);
    }

    if (!empty($city)) {
        $builder->where('city', $city);
    }

    $customers = $builder
        ->orderBy('id', 'DESC')
        ->paginate(20);

    $editableCustomerIds = [];
    foreach ($customers as $customer) {
        if ($this->canEdit($customer)) {
            $editableCustomerIds[] = (int) $customer['id'];
        }
    }

    $data = [
        'customers' => $customers,
        'pager'     => $builder->pager,
        'search'    => $search,
        'status'    => $status,
        'city'      => $city,
        'editableCustomerIds' => $editableCustomerIds,
    ];

    return view('customers/index', $data);
}

    public function create()
    {
        return view('customers/create', [
            'users' => $this->userModel->whereIn('role', ['manager', 'sales'])->findAll(),
        ]);
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
            'notes' => $this->request->getPost('notes'),
            'assigned_to' => session()->get('role') === 'sales'
                ? session()->get('user_id')
                : $this->request->getPost('assigned_to')
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

        if (!$this->canView($customer)) {
            return $this->denyAccess();
        }

        if (!$this->canEdit($customer)) {
            return $this->denyAccess();
        }

        $data = [
            'customer' => $customer
        ];

        $data['users'] = $this->userModel
            ->whereIn('role', ['manager', 'sales'])
            ->findAll();

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

        if (!$this->canEdit($customer)) {
            return $this->denyAccess();
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

        if (session()->get('role') === 'admin') {
            $data['assigned_to'] = $this->request->getPost('assigned_to');
        }

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

        if (!$this->canEdit($customer)) {
            return $this->denyAccess();
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

        if (!$this->canView($customer)) {
            return $this->denyAccess();
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
        $role = session()->get('role');
        $userId = session()->get('user_id');
        $builder = $this->customerModel;

        if ($role === 'sales') {
            $builder->where('assigned_to', $userId);
        } elseif ($role !== 'manager' && $role !== 'admin') {
            $builder->where('id', 0);
        }

        $customers = $builder->findAll();

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
