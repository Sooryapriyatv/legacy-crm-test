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

    <div class="col-md-2">
        <input
            type="text"
            name="city"
            class="form-control"
            placeholder="Filter by city..."
            value="<?= esc($city ?? '') ?>"
            maxlength="50">
    </div>

    <div class="col-md-1">
        <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-search"></i> Filter
        </button>
    </div>

    <div class="col-md-2">
        <a href="<?= base_url('customers') ?>" class="btn btn-outline-secondary w-100">
            <i class="bi bi-arrow-counterclockwise"></i> Reset
        </a>
    </div>
</form>
    </div>
</div>

<!-- Customers Table -->
<div class="card">
    <div class="card-body">

            <form
                method="POST"
                action="<?= base_url('customers/bulk-delete') ?>"
                id="bulkDeleteForm"
            >

                <?= csrf_field() ?>

                <?php if (session()->get('role') === 'admin'): ?>
                <div class="d-flex justify-content-between align-items-center mb-3">

                    <div>
                        <span id="selectedCount" class="text-muted">
                            0 selected
                        </span>
                    </div>

                    <button
                        type="submit"
                        class="btn btn-danger"
                        id="bulkDeleteBtn"
                        disabled
                    >
                        <i class="bi bi-trash"></i>
                        Delete Selected
                    </button>

                </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-hover" id="customersTable">
                <thead>
                    <tr>
                        <?php if (session()->get('role') === 'admin'): ?>
            <th>
                <input type="checkbox" id="selectAll">
            </th>
        <?php endif; ?>
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
                            <?php if (session()->get('role') === 'admin'): ?>
                            <td>
                                <input
                                    type="checkbox"
                                    name="customer_ids[]"
                                    value="<?= esc($customer['id']) ?>"
                                    class="customer-checkbox"
                                >
                            </td>
                            <?php endif; ?>
                            <td><?= esc($customer['id']) ?></td>
                            <td>
                                <a href="<?= base_url('customers/view/' . $customer['id']) ?>">
                                    <?= esc($customer['name']) ?>
                                </a>
                            </td>
                            <td><?= esc($customer['email']) ?></td>
                            <td><?= esc($customer['phone']) ?></td>
                            <td><?= esc($customer['company']) ?></td>
                            <td><?= esc($customer['city']) ?></td>
                            <td>
                                <span class="status-badge status-<?= esc($customer['status']) ?>">
                                    <?= esc($customer['status']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($customer['activities'])): ?>
                                    <small class="text-muted">
                                        <?php foreach (array_slice($customer['activities'], 0, 2) as $activity): ?>
                                            <div><?= esc($activity['action']) ?>: <?= esc($activity['description']) ?></div>
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
                                <button type="submit" formaction="<?= base_url('customers/delete/' . $customer['id']) ?>" formmethod="POST" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?= session()->get('role') === 'admin' ? '10' : '9' ?>" class="text-center text-muted">No customers found</td>
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

                </form>

    </div>
</div>


</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /*
     * Customer search
     */
    const customerSearch = document.getElementById('customerSearch');

    if (customerSearch) {

        customerSearch.addEventListener('keyup', function () {

            let searchValue = this.value.toLowerCase();

            let rows = document.querySelectorAll(
                '#customersTable tbody tr'
            );

            rows.forEach(function (row) {

                let name =
                    row.cells[1]?.textContent.toLowerCase() || '';

                let email =
                    row.cells[2]?.textContent.toLowerCase() || '';

                if (
                    name.includes(searchValue) ||
                    email.includes(searchValue)
                ) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }

            });

        });

    }


    /*
     * Bulk delete
     */
    const selectAll =
        document.getElementById('selectAll');

    const checkboxes =
        document.querySelectorAll('.customer-checkbox');

    const deleteButton =
        document.getElementById('bulkDeleteBtn');

    const selectedCount =
        document.getElementById('selectedCount');

    const bulkDeleteForm =
        document.getElementById('bulkDeleteForm');


    function updateBulkDeleteButton() {

        const selected =
            document.querySelectorAll(
                '.customer-checkbox:checked'
            );

        const count = selected.length;

        if (selectedCount) {
            selectedCount.textContent =
                count + ' selected';
        }

        if (deleteButton) {
            deleteButton.disabled = count === 0;
        }

        if (selectAll) {

            selectAll.checked =
                count > 0 &&
                count === checkboxes.length;

            selectAll.indeterminate =
                count > 0 &&
                count < checkboxes.length;
        }
    }


    /*
     * Select All
     */
    if (selectAll) {

        selectAll.addEventListener('change', function () {

            checkboxes.forEach(function (checkbox) {
                checkbox.checked = selectAll.checked;
            });

            updateBulkDeleteButton();
        });

    }


    /*
     * Individual checkboxes
     */
    checkboxes.forEach(function (checkbox) {

        checkbox.addEventListener('change', function () {
            updateBulkDeleteButton();
        });

    });


    /*
     * Confirmation
     */
    if (bulkDeleteForm) {

        bulkDeleteForm.addEventListener('submit', function (event) {

            const selected =
                document.querySelectorAll(
                    '.customer-checkbox:checked'
                );

            if (selected.length === 0) {

                event.preventDefault();

                alert('Please select at least one customer.');

                return;
            }


            const confirmed = confirm(
                'Are you sure you want to delete ' +
                selected.length +
                ' selected customer(s)?'
            );

            if (!confirmed) {
                event.preventDefault();
            }

        });

    }

});
</script>

<?= $this->include('layout/footer') ?>
