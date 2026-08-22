<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'Legacy CRM System' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .sidebar {
            min-height: 100vh;
            background: #2c3e50;
        }
        .sidebar .nav-link {
            color: #ecf0f1;
            padding: 12px 20px;
            border-radius: 5px;
            margin: 5px 10px;
        }
        .sidebar .nav-link:hover {
            background: #34495e;
            color: #fff;
        }
        .sidebar .nav-link.active {
            background: #3498db;
            color: #fff;
        }
        .content-wrapper {
            padding: 30px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        #customerGrowthChart {
            min-height: 350px;
        }

    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php if (session()->get('logged_in')): ?>
            <!-- Sidebar -->
            <div class="col-md-2 p-0 sidebar">
                <div class="p-3 text-white">
                    <h4><i class="bi bi-Building"></i> CRM System</h4>
                    <small>v2.0 Legacy</small>
                </div>
                <nav class="nav flex-column">
                    <a class="nav-link <?= uri_string() == 'dashboard' ? 'active' : '' ?>" href="<?= base_url('dashboard') ?>">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                    <a class="nav-link <?= strpos(uri_string(), 'customers') !== false ? 'active' : '' ?>" href="<?= base_url('customers') ?>">
                        <i class="bi bi-people"></i> Customers
                    </a>
                    <hr class="text-white">
                    <a class="nav-link" href="<?= base_url('logout') ?>">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </nav>
            </div>
            <?php endif; ?>

            <!-- Main Content -->
            <div class="col-md-<?= session()->get('logged_in') ? '10' : '12' ?>">
                <?php if (session()->get('logged_in')): ?>
                <!-- Top Bar -->
                <nav class="navbar navbar-light bg-light border-bottom">
                    <div class="container-fluid">
                        <span class="navbar-text">
                            Welcome, <strong><?= session()->get('username') ?></strong>
                        </span>
                    </div>
                </nav>
                <?php endif; ?>

                <div class="content-wrapper">
                    <!-- Flash Messages -->
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
