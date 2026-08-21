<?= $this->include('layout/header') ?>

<div class="container">
    <div class="row justify-content-center" style="margin-top: 100px;">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-building" style="font-size: 48px; color: #3498db;"></i>
                        <h3 class="mt-3">Legacy CRM System</h3>
                        <p class="text-muted">Sign in to your account</p>
                    </div>

                    <form action="<?= base_url('authenticate') ?>" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" placeholder="Enter username" required autofocus>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="bi bi-box-arrow-in-right"></i> Sign In
                        </button>
                    </form>

                    <div class="mt-4 text-center">
                        <small class="text-muted">
                            <strong>Demo Credentials:</strong><br>
                            Username: admin | Password: admin123
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('layout/footer') ?>
