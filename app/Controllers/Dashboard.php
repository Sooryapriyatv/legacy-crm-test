<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\ActivityModel;

class Dashboard extends BaseController
{
    public function accessDenied()
    {
        return view('errors/access_denied');
    }

    public function index()
    {
        $cache = cache();

        // Check cached dashboard data
        $dashboardData = $cache->get('dashboard_data');

        if ($dashboardData !== null) {
            // log_message('debug', 'DASHBOARD CACHE HIT');
            return view('dashboard/index', $dashboardData);
        }

        // log_message('debug', 'DASHBOARD CACHE MISS');

        $customerModel = new CustomerModel();
        $activityModel = new ActivityModel();

        $totalCustomers = $customerModel->countAllResults();
        $activeCustomers = $customerModel->where('status', 'active')->countAllResults();    
        $recentCustomers = $customerModel->orderBy('created_at', 'DESC')->limit(5)->findAll();
        
        // Inactive customers
        $inactiveCustomers = $customerModel
            ->where('status', 'inactive')
            ->countAllResults();

        // New customers this month
        $newThisMonth = $customerModel
            ->where('created_at >=', date('Y-m-01 00:00:00'))
            ->where('created_at <=', date('Y-m-t 23:59:59'))
            ->countAllResults();

        // Recent customers
        $recentCustomers = $customerModel
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->findAll();

        //customer growth line chart data
        $growth = $customerModel
            ->select("
                DATE_FORMAT(created_at, '%Y-%m') AS month_key,
                DATE_FORMAT(created_at, '%b') AS month,
                COUNT(*) AS total
            ")
            ->where(
                'created_at >=',
                date('Y-m-01 00:00:00', strtotime('-5 months'))
            )
            ->groupBy("DATE_FORMAT(created_at, '%Y-%m')")
            ->orderBy('month_key', 'ASC')
            ->findAll();

        // Status Distribution
        $statusDistribution = $customerModel
            ->select('status, COUNT(*) as total')
            ->groupBy('status')
            ->findAll();

        // Top 5 Cities
        $topCities = $customerModel
            ->select('city, COUNT(*) AS total')
            ->where('city IS NOT NULL')
            ->where('city !=', '')
            ->groupBy('city')
            ->orderBy('total', 'DESC')
            ->limit(5)
            ->findAll();

        // Recent activities
        $recentActivities = $activityModel
            ->select('
                customer_activities.action,
                customer_activities.description,
                customer_activities.created_at,
                customers.name AS customer_name
            ')
            ->join(
                'customers',
                'customers.id = customer_activities.customer_id'
            )
            ->orderBy('customer_activities.created_at', 'DESC')
            ->limit(10)
            ->findAll();

        $data = [
            'total_customers' => $totalCustomers,
            'active_customers' => $activeCustomers,
            'inactive_customers' => $inactiveCustomers,
            'recent_customers' => $recentCustomers,
            'new_this_month' => $newThisMonth,
            'customer_growth'    => $growth,
            'status_distribution' => $statusDistribution,
            'top_cities'          => $topCities,
            'recent_activities'   => $recentActivities
        ];

        // log_message(
        //     'debug',
        //     'Dashboard data before cache: ' . print_r($data, true)
        // );

        // Cache dashboard data for 1 hour
        $cache->save(
            'dashboard_data',
            $data,
            3600
        );

        

        return view('dashboard/index', $data);
    }

    public function refreshCache()
    {
        cache()->delete('dashboard_data');

        return redirect()
            ->to('/dashboard')
            ->with('success', 'Dashboard data refreshed successfully.');
    }
}
