<?= $this->include('layout/header') ?>

<h2 class="mb-4"><i class="bi bi-speedometer2"></i> Dashboard
<a href="<?= base_url('dashboard/refresh') ?>" style="float: right;"
       class="btn btn-primary">

        <i class="bi bi-arrow-clockwise"></i>
        Refresh Data

    </a>
</h2>

<div class="row mb-4">
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title">Total Customers</h5>
                <h2><?= $total_customers ?></h2>
                <p class="mb-0"><i class="bi bi-people"></i> All registered customers</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title">Active Customers</h5>
                <h2><?= $active_customers ?></h2>
                <p class="mb-0"><i class="bi bi-check-circle"></i> Currently active</p>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card text-white bg-info h-100">
            <div class="card-body">
                <h5 class="card-title">New This Month</h5>
                <h2><?= $new_this_month ?></h2>
                <p class="mb-0">
                    <i class="bi bi-person-plus"></i>
                    Added this month
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card text-white bg-danger h-100">
            <div class="card-body">
                <h5 class="card-title">Inactive Customers</h5>
                <h2><?= $inactive_customers ?></h2>
                <p class="mb-0">
                    <i class="bi bi-x-circle"></i>
                    Currently inactive
                </p>
            </div>
        </div>
    </div>

    </div>


<div class="row mb-4">

    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-lightning"></i>
                        Quick Actions
                    </h5>

                    <div>
                        <a href="<?= base_url('customers/create') ?>"
                           class="btn btn-primary me-2 mb-1">
                            <i class="bi bi-plus-circle"></i>
                            Add Customer
                        </a>
    
                         <a href="<?= base_url('customers') ?>"
                           class="btn btn-secondary">
                            <i class="bi bi-list"></i>
                            View All Customers
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>



<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-graph-up"></i>
                    Customer Growth
                </h5>
            </div>

            <div class="card-body">
                <canvas id="customerGrowthChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-pie-chart"></i>
                    Customer Status Distribution
                </h5>
            </div>

            <div class="card-body">
                <div style="height: 350px;">
                    <canvas id="statusDistributionChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row mb-4">

    <div class="col-12">

        <div class="card">

            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-bar-chart"></i>
                    Top 5 Cities
                </h5>
            </div>

            <div class="card-body">

                <div style="height: 350px;">
                    <canvas id="topCitiesChart"></canvas>
                </div>

            </div>

        </div>

    </div>

</div>

<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="bi bi-clock-history"></i>
            Recent Activity
        </h5>

        <span class="badge bg-secondary">Last 10</span>
    </div>

    <div class="card-body">

        <?php if (!empty($recent_activities)): ?>

            <div class="list-group list-group-flush">

                <?php foreach ($recent_activities as $activity): ?>

                    <div class="list-group-item px-0">

                        <div class="d-flex align-items-start">

                            <!-- Activity Icon -->
                            <div class="me-3">

                                <?php
                                $action = strtolower($activity['action']);

                                if (str_contains($action, 'create')) {
                                    $icon = 'bi-person-plus';
                                    $iconClass = 'text-success';
                                } elseif (str_contains($action, 'status')) {
                                    $icon = 'bi-arrow-repeat';
                                    $iconClass = 'text-warning';
                                } elseif (str_contains($action, 'update')) {
                                    $icon = 'bi-pencil-square';
                                    $iconClass = 'text-primary';
                                } else {
                                    $icon = 'bi-activity';
                                    $iconClass = 'text-secondary';
                                }
                                ?>

                                <i class="bi <?= $icon ?> <?= $iconClass ?> fs-4"></i>

                            </div>

                            <!-- Activity Details -->
                            <div class="flex-grow-1">

                                <div>
                                    <strong>
                                        <?= esc($activity['customer_name']) ?>
                                    </strong>

                                    <span class="ms-1">
                                        <?= esc($activity['action']) ?>
                                    </span>
                                </div>

                                <?php if (!empty($activity['description'])): ?>

                                    <div class="text-muted small mt-1">
                                        <?= esc($activity['description']) ?>
                                    </div>

                                <?php endif; ?>

                                <div class="text-muted small mt-1">

                                    <i class="bi bi-clock"></i>

                                    <?= date(
                                        'M d, Y h:i A',
                                        strtotime($activity['created_at'])
                                    ) ?>

                                </div>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <div class="text-center text-muted py-4">

                <i class="bi bi-clock-history fs-1"></i>

                <p class="mt-2 mb-0">
                    No recent activities found.
                </p>

            </div>

        <?php endif; ?>

    </div>
</div>

<div class="card mt-4">
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


<script>
    const growthLabels = <?= json_encode(
        array_column($customer_growth, 'month')
    ) ?>;

    const growthData = <?= json_encode(
        array_column($customer_growth, 'total')
    ) ?>;

    const ctx = document
        .getElementById('customerGrowthChart')
        .getContext('2d');

    new Chart(ctx, {
        type: 'line',

        data: {
            labels: growthLabels,

            datasets: [{
                label: 'Customers Added',
                data: growthData,

                borderWidth: 3,
                tension: 0.3,

                fill: false,

                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },

        options: {
            responsive: true,

            maintainAspectRatio: false,

            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Month'
                    }
                },

                y: {
                    beginAtZero: true,

                    ticks: {
                        precision: 0
                    },

                    title: {
                        display: true,
                        text: 'Number of Customers'
                    }
                }
            },

            plugins: {
                legend: {
                    display: true
                }
            }
        }
    });
</script>

<script>
    const statusLabels = <?= json_encode(
        array_map(
            'ucfirst',
            array_column($status_distribution, 'status')
        )
    ) ?>;

    const statusCounts = <?= json_encode(
        array_map(
            'intval',
            array_column($status_distribution, 'total')
        )
    ) ?>;

    const totalStatusCustomers = statusCounts.reduce(
        (sum, count) => sum + count,
        0
    );

    new Chart(
        document.getElementById('statusDistributionChart'),
        {
            type: 'pie',

            data: {
                labels: statusLabels,

                datasets: [{
                    data: statusCounts,
                    borderWidth: 1
                }]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                plugins: {
                    legend: {
                        position: 'bottom'
                    },

                    tooltip: {
                        callbacks: {
                            label: function(context) {

                                const count = context.raw;

                                const percentage =
                                    totalStatusCustomers > 0
                                        ? ((count / totalStatusCustomers) * 100).toFixed(1)
                                        : 0;

                                return `${context.label}: ${count} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        }
    );
</script>

<script>
    const cityLabels = <?= json_encode(
        array_column($top_cities, 'city')
    ) ?>;

    const cityCounts = <?= json_encode(
        array_map(
            'intval',
            array_column($top_cities, 'total')
        )
    ) ?>;

    new Chart(
        document.getElementById('topCitiesChart'),
        {
            type: 'bar',

            data: {
                labels: cityLabels,

                datasets: [{
                    label: 'Customer Count',
                    data: cityCounts,
                    borderWidth: 1
                }]
            },

            options: {
                responsive: true,

                maintainAspectRatio: false,

                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'City'
                        }
                    },

                    y: {
                        beginAtZero: true,

                        ticks: {
                            precision: 0
                        },

                        title: {
                            display: true,
                            text: 'Customer Count'
                        }
                    }
                },

                plugins: {
                    legend: {
                        display: false
                    },

                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Customers: ' + context.raw;
                            }
                        }
                    }
                }
            }
        }
    );
</script>

<?= $this->include('layout/footer') ?>
