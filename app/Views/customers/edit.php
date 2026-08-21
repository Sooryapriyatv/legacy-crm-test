<?= $this->include('layout/header') ?>

<div class="mb-4">
    <a href="<?= base_url('customers') ?>" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back to List
    </a>
</div>

<div class="card">
    <div class="card-header bg-warning">
        <h5 class="mb-0"><i class="bi bi-pencil"></i> Edit Customer #<?= $customer['id'] ?></h5>
    </div>
    <div class="card-body">
        <form action="<?= base_url('customers/update/' . $customer['id']) ?>" method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="<?= $customer['name'] ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" value="<?= $customer['email'] ?>" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="<?= $customer['phone'] ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Company</label>
                    <input type="text" name="company" class="form-control" value="<?= $customer['company'] ?>">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control" value="<?= $customer['city'] ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="active" <?= $customer['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= $customer['status'] == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        <option value="pending" <?= $customer['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="4"><?= $customer['notes'] ?></textarea>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Update Customer
                </button>
            </div>
        </form>

        <!-- Debug Info (should be removed in production) -->
        <div class="alert alert-info mt-4">
            <small><strong>Debug Info:</strong> Last updated at <?= $customer['updated_at'] ?></small>
        </div>
    </div>
</div>

<?= $this->include('layout/footer') ?>
