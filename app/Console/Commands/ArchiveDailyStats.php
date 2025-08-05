<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\Application;

class ArchiveDailyStats extends Command
{
    protected $signature = 'stats:archive';
    protected $description = 'Archive dashboard stats from 5 AM to 8 PM daily';

    public function handle()
    {
        $today = Carbon::today();
        $start = $today->copy()->setHour(5)->setMinute(0)->setSecond(0);  // 5:00 AM
        $end = $today->copy()->setHour(20)->setMinute(0)->setSecond(0);   // 8:00 PM

        $this->info("Archiving stats from {$start->format('Y-m-d H:i')} to {$end->format('Y-m-d H:i')}");

        $stats = [
            'clients_served' => Application::whereBetween('created_at', [$start, $end])->count(),
            'pending' => Application::whereBetween('created_at', [$start, $end])
                ->where('is_preapplied', true)
                ->where('entered_queue', false)
                ->where('status', 'pending')
                ->count(),
            'completed' => Application::whereBetween('created_at', [$start, $end])
                ->where('status', 'completed')
                ->count(),
            'cancelled' => Application::whereBetween('created_at', [$start, $end])
                ->where('status', 'cancelled')
                ->count(),
            'pwd_clients' => Application::whereBetween('created_at', [$start, $end])
                ->where('is_pwd', true)
                ->count(),
            'senior_clients' => Application::whereBetween('created_at', [$start, $end])
                ->where('age', '>=', 60)
                ->count(),
        ];

        DB::table('system_logs')->insert([
            'log_type' => 'daily_summary',
            'message' => "Daily Summary ({$start->format('M d, Y')} 5AM–8PM): "
                . "{$stats['clients_served']} served, "
                . "{$stats['completed']} completed, "
                . "{$stats['cancelled']} cancelled, "
                . "{$stats['pwd_clients']} PWD, "
                . "{$stats['senior_clients']} Seniors",
            'data' => json_encode($stats),
            'logged_at' => $start,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->info('✅ Stats from 5 AM to 8 PM archived successfully.');
    }
}