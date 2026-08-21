<?= $this->include('layout/header') ?>

<div class="mb-4">
    <a href="<?= base_url('customers') ?>" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back to List
    </a>
</div>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="bi bi-plus-circle"></i> Add New Customer
        </h5>
    </div>

    <div class="card-body">

        <!-- Validation Errors -->
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('customers/store') ?>" method="POST">

            <?= csrf_field() ?>

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        Name <span class="text-danger">*</span>
                    </label>

                    <input type="text"
                           name="name"
                           class="form-control"
                           value="<?= esc(old('name')) ?>"
                           required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        Email <span class="text-danger">*</span>
                    </label>

                    <input type="email"
                           name="email"
                           class="form-control"
                           value="<?= esc(old('email')) ?>"
                           required>
                </div>

            </div>

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone</label>

                    <input type="text"
                           name="phone"
                           class="form-control"
                           value="<?= esc(old('phone')) ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Company</label>

                    <input type="text"
                           name="company"
                           class="form-control"
                           value="<?= esc(old('company')) ?>">
                </div>

            </div>

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="form-label">City</label>

                    <input type="text"
                           name="city"
                           class="form-control"
                           value="<?= esc(old('city')) ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Status</label>

                    <?php $oldStatus = old('status', 'active'); ?>

                    <select name="status" class="form-select">
                        <option value="active"
                            <?= $oldStatus === 'active' ? 'selected' : '' ?>>
                            Active
                        </option>

                        <option value="inactive"
                            <?= $oldStatus === 'inactive' ? 'selected' : '' ?>>
                            Inactive
                        </option>

                        <option value="pending"
                            <?= $oldStatus === 'pending' ? 'selected' : '' ?>>
                            Pending
                        </option>
                    </select>
                </div>

            </div>

            <div class="mb-3">
                <label class="form-label">Notes</label>

                <textarea name="notes"
                          class="form-control"
                          rows="4"><?= esc(old('notes')) ?></textarea>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Create Customer
                </button>
            </div>

        </form>

    </div>
</div>

<?= $this->include('layout/footer') ?>