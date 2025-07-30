<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\Application;

class ArchiveDailyStats extends Command
{
    protected $signature = 'stats:archive';
    protected $description = 'Archive today\'s dashboard stats to system logs';

    public function handle()
    {
        $today = Carbon::today();
        $startOfDay = $today->startOfDay();
        $endOfDay = $today->endOfDay();

        $this->info("Archiving stats for {$startOfDay->format('Y-m-d')}");

        // Get stats (same logic as DashboardController)
        $stats = [
            'clients_served' => Application::whereDate('created_at', $today)->count(),
            'pending' => Application::whereDate('created_at', $today)
                ->where('is_preapplied', true)
                ->where('entered_queue', false)
                ->where('status', 'pending')
                ->count(),
            'completed' => Application::whereDate('created_at', $today)
                ->where('status', 'completed')
                ->count(),
            'cancelled' => Application::whereDate('created_at', $today)
                ->where('status', 'cancelled')
                ->count(),
            'pwd_clients' => Application::whereDate('created_at', $today)
                ->where('is_pwd', true)
                ->count(),
            'senior_clients' => Application::whereDate('created_at', $today)
                ->where('age', '>=', 60)
                ->count(),
        ];

        // Create log entry
        DB::table('system_logs')->insert([
            'log_type' => 'daily_summary',
            'message' => "Daily Summary ({$startOfDay->format('M d, Y')}): "
                . "{$stats['clients_served']} served, "
                . "{$stats['completed']} completed, "
                . "{$stats['cancelled']} cancelled, "
                . "{$stats['pwd_clients']} PWD, "
                . "{$stats['senior_clients']} Seniors",
            'data' => json_encode($stats),
            'logged_at' => $startOfDay, // The day this summary represents
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->info('✅ Daily stats archived successfully.');
    }
}