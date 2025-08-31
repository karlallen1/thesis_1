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
        $today = today();

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

    /**
     * Get dashboard stats with correct logic
     */
    private function getDashboardStats($today)
    {
        return [
            'clients_served' => Application::where('entered_queue', true)
                ->whereDate('queue_entered_at', $today)
                ->count(),

            'pending' => Application::where('is_preapplied', true)
                ->where('entered_queue', false)
                ->where('status', 'pending')
                ->count(),

            'cancelled' => Application::where('status', 'cancelled')
                ->whereNotNull('cancelled_at')
                ->whereDate('cancelled_at', $today)
                ->count(),

            'completed' => Application::where('status', 'completed')
                ->whereNotNull('completed_at')
                ->whereDate('completed_at', $today)
                ->count(),

            'pwd_clients' => Application::where('entered_queue', true)
                ->where('is_pwd', true)
                ->whereDate('queue_entered_at', $today)
                ->count(),

            'senior_clients' => Application::where('entered_queue', true)
                ->where(function ($q) {
                    $q->where('age', '>=', 60)
                      ->orWhereNotNull('senior_id')
                      ->where('senior_id', '!=', '');
                })
                ->whereDate('queue_entered_at', $today)
                ->count(),
        ];
    }

    /**
     * Hourly data for chart (based on queue entry)
     */
    private function getHourlyData($today)
    {
        $data = [];
        for ($hour = 7; $hour <= 17; $hour++) {
            $start = Carbon::parse($today)->setHour($hour)->startOfHour();
            $end = $start->copy()->endOfHour();

            $count = Application::where('entered_queue', true)
                ->whereBetween('queue_entered_at', [$start, $end])
                ->count();

            $data[] = $count;
        }
        
        // FIXED: Remove the duplicate lines that were causing infinite recursion
        return $data;
    }

    /**
     * API endpoint for real-time dashboard refresh
     */
        public function getStats()
    {
        $today = today();

        try {
            $stats = $this->getDashboardStats($today);
            $hourlyData = $this->getHourlyData($today); // Add this

            return response()->json(array_merge($stats, ['hourlyData' => $hourlyData]), 200, [
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
                'hourlyData' => array_fill(0, 11, 0)
            ], 500);
        }
    }
}