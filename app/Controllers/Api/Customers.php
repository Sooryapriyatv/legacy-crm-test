<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use CodeIgniter\HTTP\ResponseInterface;

class Customers extends BaseController
{
    protected CustomerModel $customerModel;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
    }

    /**
     * GET /api/customers
     */
    public function index()
    {
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
        $customer = $this->customerModel->find($id);

        if (!$customer) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'status' => false,
                    'message' => 'Customer not found.'
                ]);
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
        $data = $this->request->getJSON(true);

        if (!is_array($data)) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'status' => false,
                    'message' => 'Invalid JSON request.'
                ]);
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
        $customer = $this->customerModel->find($id);

        if (!$customer) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'status' => false,
                    'message' => 'Customer not found.'
                ]);
        }

        $data = $this->request->getJSON(true);

        if (!is_array($data)) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'status' => false,
                    'message' => 'Invalid JSON request.'
                ]);
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