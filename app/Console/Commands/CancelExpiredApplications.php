<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Application;
use Illuminate\Support\Facades\Log; // ✅ Added Log facade
use Illuminate\Support\Carbon;

class CancelExpiredApplications extends Command
{
    // 🔽 The command name you'll use in the terminal or scheduler
    protected $signature = 'app:cancel-expired-applications';

    // 🔽 Description shown in `php artisan list`
    protected $description = 'Cancel pre-registered applications whose QR code has expired (24 hours passed)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = Application::where('is_preapplied', true)
            ->where('entered_queue', false)
            ->where('status', 'pending')
            ->where('qr_expires_at', '<', now())
            ->update([
                'status' => 'cancelled',
                'cancelled_at' => now()
            ]);

        if ($count > 0) {
            Log::info("Automatically cancelled {$count} expired pre-applications.");
            $this->info("Cancelled {$count} expired pre-applications.");
        } else {
            $this->comment("No expired applications found.");
        }
    }
}