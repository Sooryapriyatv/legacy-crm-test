<?= $this->include('layout/header') ?>

<div class="mb-4">
    <a href="<?= base_url('customers') ?>" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back to List
    </a>
    <a href="<?= base_url('customers/edit/' . $customer['id']) ?>" class="btn btn-warning">
        <i class="bi bi-pencil"></i> Edit
    </a>
    <form action="<?= base_url('customers/delete/' . $customer['id']) ?>" method="POST" class="d-inline">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">
            <i class="bi bi-trash"></i> Delete
        </button>
    </form>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-person"></i> Customer Details</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="150">ID:</th>
                        <td><?= $customer['id'] ?></td>
                    </tr>
                    <tr>
                        <th>Name:</th>
                        <td><strong><?= $customer['name'] ?></strong></td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td><?= $customer['email'] ?></td>
                    </tr>
                    <tr>
                        <th>Phone:</th>
                        <td><?= $customer['phone'] ?: '-' ?></td>
                    </tr>
                    <tr>
                        <th>Company:</th>
                        <td><?= $customer['company'] ?: '-' ?></td>
                    </tr>
                    <tr>
                        <th>City:</th>
                        <td><?= $customer['city'] ?: '-' ?></td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td>
                            <span class="status-badge status-<?= $customer['status'] ?>">
                                <?= ucfirst($customer['status']) ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Created:</th>
                        <td><?= date('M d, Y H:i', strtotime($customer['created_at'])) ?></td>
                    </tr>
                    <tr>
                        <th>Last Updated:</th>
                        <td><?= date('M d, Y H:i', strtotime($customer['updated_at'])) ?></td>
                    </tr>
                </table>

                <?php if ($customer['notes']): ?>
                <div class="mt-3">
                    <strong>Notes:</strong>
                    <div class="p-3 bg-light rounded">
                        <?= nl2br($customer['notes']) ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Activity History</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($activities)): ?>
                    <div class="activity-timeline">
                        <?php foreach ($activities as $activity): ?>
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="d-flex justify-content-between">
                                <strong class="text-capitalize"><?= $activity['action'] ?></strong>
                                <small class="text-muted"><?= date('M d, H:i', strtotime($activity['created_at'])) ?></small>
                            </div>
                            <small class="text-muted"><?= $activity['description'] ?></small>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No activity history found</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->include('layout/footer') ?>
