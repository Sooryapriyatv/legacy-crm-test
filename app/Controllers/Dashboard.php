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
        $role = session()->get('role');
        $userId = (int) session()->get('user_id');
        $cacheKey = 'dashboard_data_' . $role . '_' . $userId;

        // Check cached dashboard data
        $dashboardData = $cache->get($cacheKey);

        if ($dashboardData !== null) {
            // log_message('debug', 'DASHBOARD CACHE HIT');
            return view('dashboard/index', $dashboardData);
        }

        // log_message('debug', 'DASHBOARD CACHE MISS');

        $customerModel = new CustomerModel();
        $activityModel = new ActivityModel();

        $scopeCustomers = static function ($model) use ($role, $userId) {
            if ($role === 'sales') {
                $model->where('assigned_to', $userId);
            }

            return $model;
        };

        $totalCustomers = $scopeCustomers($customerModel)->countAllResults();
        $activeCustomers = $scopeCustomers($customerModel)->where('status', 'active')->countAllResults();
        $recentCustomers = $scopeCustomers($customerModel)->orderBy('created_at', 'DESC')->limit(5)->findAll();
        
        // Inactive customers
        $inactiveCustomers = $scopeCustomers($customerModel)
            ->where('status', 'inactive')
            ->countAllResults();

        // New customers this month
        $newThisMonth = $scopeCustomers($customerModel)
            ->where('created_at >=', date('Y-m-01 00:00:00'))
            ->where('created_at <=', date('Y-m-t 23:59:59'))
            ->countAllResults();

        // Recent customers
        $recentCustomers = $scopeCustomers($customerModel)
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->findAll();

        //customer growth line chart data
        $growth = $scopeCustomers($customerModel)
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
        $statusDistribution = $scopeCustomers($customerModel)
            ->select('status, COUNT(*) as total')
            ->groupBy('status')
            ->findAll();

        // Top 5 Cities
        $topCities = $scopeCustomers($customerModel)
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
            ->limit(10);

        if ($role === 'sales') {
            $recentActivities->where('customers.assigned_to', $userId);
        }

        $recentActivities = $recentActivities->findAll();

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
            $cacheKey,
            $data,
            3600
        );

        

        return view('dashboard/index', $data);
    }

    public function refreshCache()
    {
        $cacheKey = 'dashboard_data_' . session()->get('role') . '_' . (int) session()->get('user_id');
        cache()->delete($cacheKey);

        return redirect()
            ->to('/dashboard')
            ->with('success', 'Dashboard data refreshed successfully.');
    }
}
