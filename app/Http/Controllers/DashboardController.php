<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index() {
        $stats = [
            'clients_served' => DB::table('queue_entries')->where('status', 'completed')->count(),
            'pending' => DB::table('queue_entries')->where('status', 'pending')->count(),
            'cancelled' => DB::table('queue_entries')->where('status', 'cancelled')->count(),
            'completed' => DB::table('queue_entries')->where('status', 'completed')->count(),
            'pwd_clients' => DB::table('queue_entries')->where('is_pwd', true)->where('status', 'completed')->count(),
            'senior_clients' => DB::table('queue_entries')->where('is_senior', true)->where('status', 'completed')->count(),
            'avg_wait_time' => DB::table('queue_entries')
                ->whereNotNull('served_at')
                ->avg(DB::raw('TIMESTAMPDIFF(MINUTE, created_at, served_at)')),
            'longest_wait' => DB::table('queue_entries')
                ->whereNotNull('served_at')
                ->max(DB::raw('TIMESTAMPDIFF(MINUTE, created_at, served_at)')),
        ];

        return view('admin.dashboard-main', compact('stats'));
    }
}
