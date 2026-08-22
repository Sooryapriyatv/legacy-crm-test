<?= $this->include('layout/header') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-people"></i> Customers</h2>
    <div>
        <a href="<?= base_url('customers/export') ?>" class="btn btn-success">
            <i class="bi bi-download"></i> Export CSV
        </a>
        <?php if (session()->get('role') === 'admin'): ?>
        <a href="<?= base_url('customers/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add Customer
        </a>
        <?php endif; ?>
    </div>
</div>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
<!-- Search & Filter Form -->
<div class="card mb-4">
    <div class="card-body">
       <form method="GET" action="<?= base_url('customers') ?>" class="row g-3" id="filterForm">
    <div class="col-md-4">
        <input
            type="text"
            name="search"
            class="form-control"
            placeholder="Search name or email..."
            value="<?= esc($search ?? '') ?>"
            id="customerSearch"
            maxlength="100">
    </div>

    <div class="col-md-3">
        <select name="status" class="form-select">
            <option value="">All Status</option>
            <option value="active" <?= isset($status) && $status == 'active' ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= isset($status) && $status == 'inactive' ? 'selected' : '' ?>>Inactive</option>
            <option value="pending" <?= isset($status) && $status == 'pending' ? 'selected' : '' ?>>Pending</option>
        </select>
    </div>

    <div class="col-md-3">
        <input
            type="text"
            name="city"
            class="form-control"
            placeholder="Filter by city..."
            value="<?= esc($city ?? '') ?>"
            maxlength="50">
    </div>

    <div class="col-md-2">
        <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-search"></i> Filter
        </button>
    </div>
</form>
    </div>
</div>

<!-- Customers Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="customersTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Company</th>
                        <th>City</th>
                        <th>Status</th>
                        <th>Recent Activities</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($customers)): ?>
                        <?php foreach ($customers as $customer): ?>
                        <tr>
                            <td><?= $customer['id'] ?></td>
                            <td>
                                <a href="<?= base_url('customers/view/' . $customer['id']) ?>">
                                    <?= $customer['name'] ?>
                                </a>
                            </td>
                            <td><?= $customer['email'] ?></td>
                            <td><?= $customer['phone'] ?></td>
                            <td><?= $customer['company'] ?></td>
                            <td><?= $customer['city'] ?></td>
                            <td>
                                <span class="status-badge status-<?= $customer['status'] ?>">
                                    <?= $customer['status'] ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($customer['activities'])): ?>
                                    <small class="text-muted">
                                        <?php foreach (array_slice($customer['activities'], 0, 2) as $activity): ?>
                                            <div><?= $activity['action'] ?>: <?= $activity['description'] ?></div>
                                        <?php endforeach; ?>
                                    </small>
                                <?php else: ?>
                                    <small class="text-muted">No activities</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= base_url('customers/view/' . $customer['id']) ?>" class="btn btn-sm btn-info" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <?php
                                $canEdit = in_array((int) $customer['id'], $editableCustomerIds ?? [], true);
                                ?>
                                <?php if ($canEdit): ?>
                                <a href="<?= base_url('customers/edit/' . $customer['id']) ?>" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <?php endif; ?>
                                <?php if (session()->get('role') === 'admin'): ?>
                                <a href="<?= base_url('customers/delete/' . $customer['id']) ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted">No customers found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($pager): ?>
            <div class="mt-3">
                <?= $pager->links() ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.getElementById('customerSearch').addEventListener('keyup', function() {
    let searchValue = this.value.toLowerCase();
    let rows = document.querySelectorAll('#customersTable tbody tr');

    rows.forEach(function(row) {
        let name = row.cells[1]?.textContent.toLowerCase() || '';
        let email = row.cells[2]?.textContent.toLowerCase() || '';

        if (name.includes(searchValue) || email.includes(searchValue)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>

<?= $this->include('layout/footer') ?>
