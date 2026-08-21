<?= $this->include('layout/header') ?>

<h2 class="mb-4"><i class="bi bi-speedometer2"></i> Dashboard</h2>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title">Total Customers</h5>
                <h2><?= $total_customers ?></h2>
                <p class="mb-0"><i class="bi bi-people"></i> All registered customers</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title">Active Customers</h5>
                <h2><?= $active_customers ?></h2>
                <p class="mb-0"><i class="bi bi-check-circle"></i> Currently active</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h5 class="card-title">Quick Actions</h5>
                <div class="d-grid gap-2 mt-3">
                    <a href="<?= base_url('customers/create') ?>" class="btn btn-light">
                        <i class="bi bi-plus-circle"></i> Add Customer
                    </a>
                    <a href="<?= base_url('customers') ?>" class="btn btn-light">
                        <i class="bi bi-list"></i> View All
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-clock-history"></i> Recent Customers</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Company</th>
                        <th>Status</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recent_customers)): ?>
                        <?php foreach ($recent_customers as $customer): ?>
                        <tr>
                            <td><?= $customer['id'] ?></td>
                            <td><?= $customer['name'] ?></td>
                            <td><?= $customer['email'] ?></td>
                            <td><?= $customer['company'] ?></td>
                            <td>
                                <span class="status-badge status-<?= $customer['status'] ?>">
                                    <?= ucfirst($customer['status']) ?>
                                </span>
                            </td>
                            <td><?= date('M d, Y', strtotime($customer['created_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No customers found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->include('layout/footer') ?>
