<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Application;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->format('Y-m-d');

        try {
            $stats = $this->getDashboardStats($today);
            $hourlyData = $this->getHourlyData($today);

            return view('admin.dashboard-main', compact('stats', 'hourlyData'));

        } catch (\Exception $e) {
            Log::error('Dashboard data fetch failed: ' . $e->getMessage());
            $stats = [
                'clients_served' => 0,
                'pending' => 0,
                'cancelled' => 0,
                'completed' => 0,
                'pwd_clients' => 0,
                'senior_clients' => 0,
            ];
            $hourlyData = array_fill(0, 11, 0);
            return view('admin.dashboard-main', compact('stats', 'hourlyData'));
        }
    }

    private function getDashboardStats($today)
    {
        $stats = [
            'clients_served' => Application::whereDate('created_at', $today)->count(),

            'pending' => Application::whereDate('created_at', $today)
                ->where('is_preapplied', true)
                ->where('entered_queue', false)
                ->where('status', 'pending')
                ->count(),

            'cancelled' => Application::whereDate('created_at', $today)
                ->where('status', 'cancelled')
                ->count(),

            'completed' => Application::whereDate('created_at', $today)
                ->where('status', 'completed')
                ->count(),

            'pwd_clients' => Application::whereDate('created_at', $today)
                ->where('is_pwd', true)
                ->count(),

            'senior_clients' => Application::whereDate('created_at', $today)
                ->where('age', '>=', 60)
                ->count(),
        ];

        Log::info('📊 Dashboard Stats Calculated:', $stats);
        return $stats;
    }

    private function getHourlyData($today)
    {
        $hourlyData = [];
        for ($hour = 7; $hour <= 17; $hour++) {
            $start = Carbon::parse($today)->setHour($hour)->startOfHour();
            $end = $start->copy()->endOfHour();
            $count = Application::whereBetween('created_at', [$start, $end])
                ->whereIn('status', ['completed', 'serving', 'cancelled'])
                ->count();
            $hourlyData[] = $count;
        }
        return $hourlyData;
    }

    public function getStats()
    {
        $today = now()->format('Y-m-d');

        try {
            Cache::forget("dashboard_stats_{$today}");
            $stats = $this->getDashboardStats($today);

            return response()->json($stats, 200, [
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);

        } catch (\Exception $e) {
            Log::error('Dashboard stats API failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to load stats',
                'clients_served' => 0,
                'pending' => 0,
                'cancelled' => 0,
                'completed' => 0,
                'pwd_clients' => 0,
                'senior_clients' => 0,
            ], 500);
        }
    }
}