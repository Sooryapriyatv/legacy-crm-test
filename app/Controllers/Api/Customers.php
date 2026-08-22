<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class Customers extends BaseController
{
    protected CustomerModel $customerModel;
    protected UserModel $userModel;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
        $this->userModel = new UserModel();
    }

    protected function currentUser(): ?array
    {
        return $this->userModel->find((int) ($this->request->jwtUser->sub ?? 0));
    }

    protected function canEdit(array $customer, array $user): bool
    {
        return $user['role'] === 'admin'
            || ($user['role'] === 'sales' && (int) $customer['assigned_to'] === (int) $user['id'])
            || ($user['role'] === 'manager' && $this->userModel
                ->where('id', $customer['assigned_to'] ?? 0)
                ->where('manager_id', $user['id'])
                ->first() !== null);
    }

    protected function denied()
    {
        return $this->response->setStatusCode(403)->setJSON([
            'status' => false,
            'message' => 'Access denied.'
        ]);
    }

    protected function validateCustomerData(array $data, ?int $customerId = null): array
    {
        $rules = [
            'name' => 'required|min_length[2]|max_length[100]|regex_match[/^[a-zA-Z\s]+$/]',
            'email' => 'required|valid_email|max_length[150]',
            'phone' => 'permit_empty|regex_match[/^[0-9]+$/]|max_length[20]',
            'company' => 'permit_empty|max_length[150]',
            'city' => 'permit_empty|max_length[100]|regex_match[/^[a-zA-Z\s]+$/]',
            'status' => 'required|in_list[active,inactive,pending]',
            'notes' => 'permit_empty|max_length[1000]',
        ];

        $validation = service('validation');
        $validation->setRules($rules);

        if (!$validation->run($data)) {
            return $validation->getErrors();
        }

        $emailQuery = $this->customerModel->where('email', $data['email']);
        if ($customerId !== null) {
            $emailQuery->where('id !=', $customerId);
        }

        if ($emailQuery->first()) {
            return ['email' => 'The email field must contain a unique value.'];
        }

        return [];
    }

    /**
     * GET /api/customers
     */
    public function index()
    {
        $user = $this->currentUser();
        if (!$user) return $this->denied();
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));

        $perPage = (int) ($this->request->getGet('per_page') ?? 20);

        $perPage = max(1, min($perPage, 100));

        $status = $this->request->getGet('status');
        $city = $this->request->getGet('city');

        $sort = $this->request->getGet('sort') ?? 'id';
        $order = strtolower(
            $this->request->getGet('order') ?? 'asc'
        );

        $allowedSorts = [
            'id',
            'name',
            'email',
            'phone',
            'company',
            'city',
            'status',
            'created_at'
        ];

        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'id';
        }

        if (!in_array($order, ['asc', 'desc'], true)) {
            $order = 'asc';
        }

        $builder = $this->customerModel;

        if ($status !== null && $status !== '') {
            $builder->where('status', $status);
        }

        if ($user['role'] === 'sales') {
            $builder->where('assigned_to', $user['id']);
        } elseif (!in_array($user['role'], ['admin', 'manager'], true)) {
            return $this->denied();
        }

        if ($city !== null && $city !== '') {
            $builder->where('city', $city);
        }

        $builder->orderBy($sort, $order);

        $customers = $builder->paginate($perPage, 'default', $page);

        $pager = $this->customerModel->pager;

        return $this->response
            ->setStatusCode(200)
            ->setJSON([
                'status' => true,
                'data' => $customers,
                'pagination' => [
                    'current_page' => $pager->getCurrentPage(),
                    'per_page' => $perPage,
                    'total' => $pager->getTotal(),
                    'total_pages' => $pager->getPageCount()
                ]
            ]);
    }

    /**
     * GET /api/customers/{id}
     */
    public function show($id)
    {
        $user = $this->currentUser();
        if (!$user) return $this->denied();
        $customer = $this->customerModel->find($id);

        if (!$customer) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'status' => false,
                    'message' => 'Customer not found.'
                ]);
        }

        if ($user['role'] === 'sales' && (int) $customer['assigned_to'] !== (int) $user['id']) {
            return $this->denied();
        }

        return $this->response
            ->setStatusCode(200)
            ->setJSON([
                'status' => true,
                'data' => $customer
            ]);
    }

    /**
     * POST /api/customers
     */
    public function create()
    {
        $user = $this->currentUser();
        if (!$user || $user['role'] !== 'admin') return $this->denied();
        $data = $this->request->getJSON(true);

        if (!is_array($data)) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'status' => false,
                    'message' => 'Invalid JSON request.'
                ]);
        }

        $allowedFields = ['name', 'email', 'phone', 'company', 'city', 'status', 'notes'];
        if ($user['role'] === 'admin') {
            $allowedFields[] = 'assigned_to';
        }
        $data = array_intersect_key($data, array_flip($allowedFields));

        if (array_key_exists('assigned_to', $data)) {
            if ($data['assigned_to'] === null || $data['assigned_to'] === '') {
                $data['assigned_to'] = null;
            } elseif (!$this->userModel->whereIn('role', ['manager', 'sales'])->find((int) $data['assigned_to'])) {
                return $this->response
                    ->setStatusCode(422)
                    ->setJSON(['status' => false, 'message' => 'Validation failed.', 'errors' => ['assigned_to' => 'Invalid assignee.']]);
            }
        }

        $errors = $this->validateCustomerData($data);
        if ($errors) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON(['status' => false, 'message' => 'Validation failed.', 'errors' => $errors]);
        }

        if (!$this->customerModel->insert($data)) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'status' => false,
                    'message' => 'Unable to create customer.',
                    'errors' => $this->customerModel->errors()
                ]);
        }

        $id = $this->customerModel->getInsertID();

        $customer = $this->customerModel->find($id);

        return $this->response
            ->setStatusCode(201)
            ->setJSON([
                'status' => true,
                'message' => 'Customer created successfully.',
                'data' => $customer
            ]);
    }

    /**
     * PUT /api/customers/{id}
     */
    public function update($id)
    {
        $user = $this->currentUser();
        if (!$user) return $this->denied();
        $customer = $this->customerModel->find($id);

        if (!$customer) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'status' => false,
                    'message' => 'Customer not found.'
                ]);
        }

        if (!$this->canEdit($customer, $user)) return $this->denied();

        $data = $this->request->getJSON(true);

        if (!is_array($data)) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'status' => false,
                    'message' => 'Invalid JSON request.'
                ]);
        }

        $allowedFields = ['name', 'email', 'phone', 'company', 'city', 'status', 'notes'];
        if ($user['role'] === 'admin') {
            $allowedFields[] = 'assigned_to';
        }
        $data = array_intersect_key($data, array_flip($allowedFields));

        $errors = $this->validateCustomerData($data, (int) $id);
        if ($errors) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON(['status' => false, 'message' => 'Validation failed.', 'errors' => $errors]);
        }

        if (array_key_exists('assigned_to', $data)) {
            if ($data['assigned_to'] === null || $data['assigned_to'] === '') {
                $data['assigned_to'] = null;
            } elseif (!$this->userModel->whereIn('role', ['manager', 'sales'])->find((int) $data['assigned_to'])) {
                return $this->response
                    ->setStatusCode(422)
                    ->setJSON(['status' => false, 'message' => 'Validation failed.', 'errors' => ['assigned_to' => 'Invalid assignee.']]);
            }
        }

        if (!$this->customerModel->update($id, $data)) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'status' => false,
                    'message' => 'Unable to update customer.',
                    'errors' => $this->customerModel->errors()
                ]);
        }

        return $this->response
            ->setStatusCode(200)
            ->setJSON([
                'status' => true,
                'message' => 'Customer updated successfully.',
                'data' => $this->customerModel->find($id)
            ]);
    }

    /**
     * DELETE /api/customers/{id}
     */
    public function delete($id)
    {
        $user = $this->currentUser();
        if (!$user || $user['role'] !== 'admin') return $this->denied();
        $customer = $this->customerModel->find($id);

        if (!$customer) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'status' => false,
                    'message' => 'Customer not found.'
                ]);
        }

        if (!$this->customerModel->delete($id)) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'status' => false,
                    'message' => 'Unable to delete customer.'
                ]);
        }

        return $this->response
            ->setStatusCode(200)
            ->setJSON([
                'status' => true,
                'message' => 'Customer deleted successfully.'
            ]);
    }
}