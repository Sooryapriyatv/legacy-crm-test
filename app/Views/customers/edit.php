<?= $this->include('layout/header') ?>

<div class="mb-4">
    <a href="<?= base_url('customers') ?>" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back to List
    </a>
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


<div class="card">
    <div class="card-header bg-warning">
        <h5 class="mb-0"><i class="bi bi-pencil"></i> Edit Customer #<?= esc($customer['id']) ?></h5>
    </div>
    <div class="card-body">
        <form action="<?= base_url('customers/update/' . $customer['id']) ?>" method="POST">
            <?= csrf_field() ?>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="<?= esc(old('name', $customer['name'])) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" value="<?= esc(old('email', $customer['email'])) ?>" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="<?= esc(old('phone', $customer['phone'])) ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Company</label>
                    <input type="text" name="company" class="form-control" value="<?= esc(old('company', $customer['company'])) ?>">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control" value="<?= esc(old('city', $customer['city'])) ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="active" <?= esc($customer['status']) == 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= esc($customer['status']) == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        <option value="pending" <?= esc($customer['status']) == 'pending' ? 'selected' : '' ?>>Pending</option>
                    </select>
                </div>
            </div>

            <?php if (session()->get('role') === 'admin'): ?>
            <div class="mb-3">
                <label class="form-label">Assign To</label>
                <select name="assigned_to" class="form-select">
                    <option value="">Unassigned</option>
                    <?php foreach ($users ?? [] as $user): ?>
                        <option value="<?= esc($user['id']) ?>" <?= old('assigned_to', $customer['assigned_to']) == $user['id'] ? 'selected' : '' ?>>
                            <?= esc($user['name']) ?> (<?= esc($user['role']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <div class="mb-3">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="4"><?= esc($customer['notes']) ?></textarea>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Update Customer
                </button>
            </div>
        </form>

        <!-- Debug Info (should be removed in production) -->
        <!-- <div class="alert alert-info mt-4">
            <small><strong>Debug Info:</strong> Last updated at <?= $customer['updated_at'] ?></small>
        </div> -->
    </div>
</div>

<?= $this->include('layout/footer') ?>
