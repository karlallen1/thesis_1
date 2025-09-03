<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\QueueState;
use App\Models\QueueCounter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class QueueController extends Controller
{
    /**
     * KIOSK STORE METHOD - Direct queue entry without QR/Email
     */
    public function store(Request $request)
    {
        try {
            Log::info('Starting kiosk application submission', ['email' => $request->email]);

            $request->validate([
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'email' => [
                    'required',
                    'email',
                    'regex:/^[\w\.-]+@(gmail\.com|yahoo\.com|outlook\.com|icloud\.com)$/'
                ],
                'contact' => [
                    'required',
                    'regex:/^\+63\s?\d{3}\s?\d{3}\s?\d{4}$/'
                ],
                'birthdate' => [
                    'required',
                    'date',
                    'before_or_equal:2007-01-01',
                    'before_or_equal:today',
                    'after:100 years ago',
                    function ($attribute, $value, $fail) {
                        if (Carbon::parse($value)->gte(Carbon::parse('2008-12-31'))) {
                            $fail('Sorry, you did not meet age requirements');
                        }
                    },
                ],
                'is_pwd' => 'nullable|in:yes,no',
                'pwd_id' => 'nullable|string|max:50',
                'senior_id' => 'nullable|string|max:50',
                'service_type' => 'required|string|max:255'
            ], [
                'email.regex' => 'Email must be from gmail.com, yahoo.com, outlook.com, or icloud.com',
                'contact.regex' => 'Contact must be in +63 format with 10 digits',
                'birthdate.before_or_equal' => 'Sorry, you did not meet age requirements',
                'birthdate.after' => 'Invalid birthdate'
            ]);

            $age = Carbon::parse($request->birthdate)->age;
            $queueNumber = $this->generateQueueNumber();

            $application = Application::create([
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'contact' => $request->contact,
                'birthdate' => $request->birthdate,
                'age' => $age,
                'is_pwd' => $request->is_pwd === 'yes',
                'pwd_id' => $request->pwd_id,
                'senior_id' => $request->senior_id,
                'service_type' => $request->service_type,
                'status' => 'pending',
                'is_preapplied' => false,
                'entered_queue' => true,
                'queue_number' => $queueNumber,
                'queue_entered_at' => now(),
                'qr_token' => null,
                'qr_expires_at' => null,
            ]);

            $this->printQueueTicket($application);

            return response()->json([
                'success' => true,
                'message' => 'Welcome to the queue!',
                'application_id' => $application->id,
                'queue_number' => $queueNumber,
                'is_priority' => $application->isPriority(),
                'priority_type' => $application->getPriorityType(),
                'estimated_wait' => $this->calculateEstimatedWait($application)
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Kiosk submission failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Submission failed. Please try again.'
            ], 500);
        }
    }

    /**
     * QR SCAN ENTRY - For pre-registered users (handles web redirect)
     */
    public function enterViaQR(Request $request)
    {
        try {
            $token = $request->get('token');

            Log::info('QR Scan Entry Attempt', [
                'token' => $token,
                'full_url' => $request->fullUrl(),
                'all_params' => $request->all()
            ]);

            if (!$token) {
                Log::warning('No token provided');
                return redirect()->route('scan.qr')->with('error', 'Invalid QR code - no token provided.');
            }

            $application = Application::where('qr_token', $token)->first();
            if (!$application) {
                Log::warning('Application not found for token', ['token' => $token]);
                return redirect()->route('scan.qr')->with('error', 'Invalid QR code - application not found.');
            }

            if ($application->entered_queue) {
                Log::info('Application already in queue');
                return redirect()->route('scan.qr')->with('error', 'You are already in the queue.');
            }

            if (!$application->isQrValid()) {
                Log::info('QR code expired');
                return redirect()->route('scan.qr')->with('error', 'QR code has expired. Please apply again.');
            }

            // Assign queue number
            $queueNumber = $this->generateQueueNumber();
            $application->update([
                'entered_queue' => true,
                'queue_number' => $queueNumber,
                'queue_entered_at' => now()
            ]);

            Log::info('Application successfully entered queue', [
                'id' => $application->id,
                'queue_number' => $queueNumber
            ]);

            // Redirect to welcome screen
            return redirect()->route('user.online.welcome', $application->id);
        } catch (\Exception $e) {
            Log::error('QR scan failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('scan.qr')->with('error', 'Scan failed. Please try again.');
        }
    }

    /**
     * Show welcome screen after QR scan
     */
    public function showWelcome($id)
    {
        try {
            $application = Application::findOrFail($id);

            if (!$application->entered_queue) {
                return redirect()->route('scan.qr')->with('error', 'Application not in queue.');
            }

            return view('user.online.welcome', compact('application'));
        } catch (\Exception $e) {
            Log::error('Welcome page error: ' . $e->getMessage());
            return redirect()->route('scan.qr')->with('error', 'Application not found.');
        }
    }

    /**
     * Print ticket (used by iframe for auto-print)
     */
    public function printTicket($id)
{
    try {
        // 🔥 Clean any previous output (critical for kiosk printing)
        if (ob_get_level()) {
            ob_clean();
        }

        $application = Application::findOrFail($id);

        return view('user.online.queue-ticket', compact('application'));
    } catch (\Exception $e) {
        // 🔥 Clean again before error response
        if (ob_get_level()) {
            ob_clean();
        }

        Log::error('Ticket print error: ' . $e->getMessage());
        return response('Application not found.', 404);
    }
}

    /**
     * Get current queue status (for admin dashboard)
     */
    public function index(Request $request)
    {
        try {
            $query = Application::where('entered_queue', true)
                ->where('status', 'pending')
                ->orderBy('queue_number');

            if ($request->has('service_type') && $request->service_type) {
                $query->where('service_type', $request->service_type);
            }

            $allApplications = $query->get();

            Log::debug('All applications in queue:', [
                'count' => $allApplications->count(),
                'applications' => $allApplications->map(fn($app) => [
                    'id' => $app->id,
                    'queue_number' => $app->queue_number,
                    'name' => $app->full_name,
                    'is_pwd' => $app->is_pwd,
                    'senior_id' => $app->senior_id,
                    'age' => $app->age,
                    'isPriority' => $app->isPriority(),
                    'priority_type' => $app->getPriorityType(),
                ])->toArray()
            ]);

            $priorityApplications = $allApplications->filter(fn($app) => $app->isPriority());
            $regularApplications = $allApplications->filter(fn($app) => !$app->isPriority());

            $nowServingApp = QueueState::getNowServing();

            $cancelledCount = Application::where('entered_queue', true)
                ->where('status', 'cancelled')
                ->whereDate('updated_at', today())
                ->count();

            $formattedPriority = $priorityApplications->map(fn($app) => [
                'id' => $app->id,
                'queue_number' => $app->queue_number,
                'name' => $app->full_name,
                'service_type' => $app->service_type,
                'is_pwd' => $app->is_pwd,
                'senior_id' => $app->senior_id,
                'age' => $app->age,
                'priority_type' => $app->getPriorityType(),
            ])->values();

            $formattedRegular = $regularApplications->map(fn($app) => [
                'id' => $app->id,
                'queue_number' => $app->queue_number,
                'name' => $app->full_name,
                'service_type' => $app->service_type,
            ])->values();

            $nowServingData = null;
            if ($nowServingApp) {
                $nowServingData = [
                    'id' => $nowServingApp->id,
                    'queue_number' => $nowServingApp->queue_number,
                    'full_name' => $nowServingApp->full_name,
                    'service_type' => $nowServingApp->service_type,
                    'email' => $nowServingApp->email,
                    'contact' => $nowServingApp->contact,
                    'birthdate' => $nowServingApp->birthdate ? $nowServingApp->birthdate->format('Y-m-d') : null,
                    'age' => $nowServingApp->age,
                    'is_pwd' => $nowServingApp->is_pwd,
                    'pwd_id' => $nowServingApp->pwd_id,
                    'senior_id' => $nowServingApp->senior_id,
                    'priority_type' => $nowServingApp->getPriorityType(),
                    'entry_type' => $nowServingApp->is_preapplied ? 'Pre-registered' : 'Walk-in',
                    'queue_entered_at' => $nowServingApp->queue_entered_at ? $nowServingApp->queue_entered_at->format('h:i A') : null,
                    'status' => ucfirst($nowServingApp->status),
                ];
            }

            return response()->json([
                'now_serving' => $nowServingData,
                'priority' => $formattedPriority,
                'regular' => $formattedRegular,
                'cancelled' => $cancelledCount,
            ]);
        } catch (\Exception $e) {
            Log::error('Queue index failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['now_serving' => null, 'priority' => [], 'regular' => [], 'cancelled' => 0], 500);
        }
    }

    /**
     * Public Queue Display Data
     */
    public function displayData(Request $request)
    {
        try {
            $applications = Application::where('entered_queue', true)
                ->where('status', 'pending')
                ->orderBy('queue_number')
                ->get();

            $priority = $applications->filter(fn($app) => $app->isPriority())->take(5);
            $regular = $applications->filter(fn($app) => !$app->isPriority())->take(5);

            $nowServing = QueueState::getNowServing();

            $formattedNowServing = $nowServing ? [
                'id' => $nowServing->id,
                'queue_number' => $nowServing->queue_number,
                'full_name' => $nowServing->full_name,
                'service_type' => $nowServing->service_type,
                'is_priority' => $nowServing->isPriority(),
                'priority_type' => $nowServing->getPriorityType(),
            ] : null;

            $nextPriority = $priority->map(fn($app) => [
                'queue_number' => $app->queue_number,
                'name' => $app->full_name,
                'service_type' => $app->service_type,
                'is_priority' => true,
                'priority_type' => $app->getPriorityType(),
            ])->values();

            $nextRegular = $regular->map(fn($app) => [
                'queue_number' => $app->queue_number,
                'name' => $app->full_name,
                'service_type' => $app->service_type,
                'is_priority' => false,
            ])->values();

            return response()->json([
                'now_serving' => $formattedNowServing,
                'priority' => $nextPriority,
                'regular' => $nextRegular,
            ]);
        } catch (\Exception $e) {
            Log::error('Public queue display data failed', ['error' => $e->getMessage()]);
            return response()->json(['now_serving' => null, 'priority' => [], 'regular' => []], 500);
        }
    }

    /**
     * Call next person
     */
    public function next(Request $request)
    {
        try {
            $applications = Application::where('entered_queue', true)
                ->where('status', 'pending')
                ->orderBy('queue_number')
                ->get();

            $priorityApps = $applications->filter(fn($app) => $app->isPriority());
            $regularApps = $applications->filter(fn($app) => !$app->isPriority());

            $nextApplication = null;
            if ($priorityApps->isNotEmpty()) {
                $nextApplication = $priorityApps->sortBy('queue_number')->first();
            } elseif ($regularApps->isNotEmpty()) {
                $nextApplication = $regularApps->sortBy('queue_number')->first();
            }

            if (!$nextApplication) {
                return response()->json(['success' => false, 'message' => 'No one in queue.']);
            }

            QueueState::setNowServing($nextApplication->id);
            $nextApplication->update(['status' => 'serving']);

            Log::info('Called next in queue', [
                'application_id' => $nextApplication->id,
                'queue_number' => $nextApplication->queue_number,
                'is_priority' => $nextApplication->isPriority(),
                'priority_type' => $nextApplication->getPriorityType(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Next person called.',
                'now_serving' => [
                    'id' => $nextApplication->id,
                    'queue_number' => $nextApplication->queue_number,
                    'full_name' => $nextApplication->full_name,
                    'service_type' => $nextApplication->service_type,
                    'is_priority' => $nextApplication->isPriority(),
                    'priority_type' => $nextApplication->getPriorityType(),
                    'contact' => $nextApplication->contact,
                    'email' => $nextApplication->email
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Next queue failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to call next person.'], 500);
        }
    }

    /**
     * Complete application
     */
    public function complete(Request $request, $id)
    {
        try {
            $application = Application::findOrFail($id);
            $application->update(['status' => 'completed', 'completed_at' => now()]);

            if (QueueState::getNowServing()?->id == $id) {
                QueueState::clearNowServing();
            }

            Log::info('Application completed', ['application_id' => $id]);
            return response()->json(['success' => true, 'message' => 'Service completed successfully.']);
        } catch (\Exception $e) {
            Log::error('Complete application failed', ['error' => $e->getMessage(), 'id' => $id]);
            return response()->json(['success' => false, 'message' => 'Failed to complete service.'], 500);
        }
    }

    /**
     * Cancel application
     */
    public function cancel(Request $request, $id)
    {
        try {
            $application = Application::findOrFail($id);
            $application->update(['status' => 'cancelled', 'cancelled_at' => now()]);

            if (QueueState::getNowServing()?->id == $id) {
                QueueState::clearNowServing();
            }

            Log::info('Application cancelled', ['application_id' => $id]);
            return response()->json(['success' => true, 'message' => 'Application cancelled successfully.']);
        } catch (\Exception $e) {
            Log::error('Cancel application failed', ['error' => $e->getMessage(), 'id' => $id]);
            return response()->json(['success' => false, 'message' => 'Failed to cancel application.'], 500);
        }
    }

    /**
     * Requeue application
     */
    public function requeue(Request $request, $id)
    {
        try {
            $application = Application::findOrFail($id);
            $application->update([
                'status' => 'pending',
                'queue_number' => $this->generateQueueNumber()
            ]);

            if (QueueState::getNowServing()?->id == $id) {
                QueueState::clearNowServing();
            }

            Log::info('Application requeued', ['application_id' => $id]);
            return response()->json(['success' => true, 'message' => 'Application requeued successfully.']);
        } catch (\Exception $e) {
            Log::error('Requeue application failed', ['error' => $e->getMessage(), 'id' => $id]);
            return response()->json(['success' => false, 'message' => 'Failed to requeue application.'], 500);
        }
    }

    /**
     * Complete currently serving
     */
    public function completeNow(Request $request)
    {
        $nowServing = QueueState::getNowServing();
        if (!$nowServing) return response()->json(['success' => false, 'message' => 'No one is currently being served.']);
        return $this->complete($request, $nowServing->id);
    }

    /**
     * Cancel currently serving
     */
    public function cancelNow(Request $request)
    {
        $nowServing = QueueState::getNowServing();
        if (!$nowServing) return response()->json(['success' => false, 'message' => 'No one is currently being served.']);
        return $this->cancel($request, $nowServing->id);
    }

    /**
     * Requeue currently serving
     */
    public function requeueNow(Request $request)
    {
        $nowServing = QueueState::getNowServing();
        if (!$nowServing) return response()->json(['success' => false, 'message' => 'No one is currently being served.']);
        QueueState::clearNowServing();
        return $this->requeue($request, $nowServing->id);
    }

    /**
     * Generate next queue number (atomic & daily reset)
     */
    private function generateQueueNumber()
    {
        return DB::transaction(function () {
            $counter = QueueCounter::firstOrCreate(['date' => today()], ['counter' => 0]);
            $locked = QueueCounter::where('id', $counter->id)->lockForUpdate()->first();
            if (!$locked) throw new \Exception('Queue counter not found.');
            $locked->increment('counter');
            return $locked->counter;
        });
    }

    /**
     * Calculate estimated wait time
     */
    private function calculateEstimatedWait($application)
    {
        $position = Application::where('entered_queue', true)
            ->where('status', 'pending')
            ->where('queue_number', '<', $application->queue_number)
            ->count();

        $estimatedMinutes = $position * 7;
        if ($estimatedMinutes <= 0) return 'You\'re next!';
        if ($estimatedMinutes < 5) return 'Less than 5 minutes';
        if ($estimatedMinutes < 60) return $estimatedMinutes . ' minutes';
        $hours = floor($estimatedMinutes / 60);
        $minutes = $estimatedMinutes % 60;
        return $hours . 'h ' . ($minutes > 0 ? $minutes . 'm' : '');
    }

    /**
     * Print queue ticket (placeholder)
     */
    private function printQueueTicket($application)
    {
        Log::info('Printing queue ticket', [
            'queue_number' => $application->queue_number,
            'service_type' => $application->service_type,
            'applicant' => $application->full_name,
            'is_priority' => $application->isPriority(),
            'estimated_wait' => $this->calculateEstimatedWait($application)
        ]);
    }

    /**
     * Handle QR scan and show welcome screen (web view version)
     */
    public function handleScan(Request $request)
    {
        $token = $request->get('token');

        // If no token, show scanner page
        if (!$token) {
            return view('user.online.scan-qr');
        }

        try {
            $application = Application::where('qr_token', $token)->first();

            if (!$application) {
                return view('user.online.scan-qr', [
                    'error' => 'Invalid QR code - application not found.'
                ]);
            }

            if ($application->entered_queue) {
                return view('user.online.scan-qr', [
                    'error' => 'You are already in the queue.'
                ]);
            }

            if (!$application->isQrValid()) {
                return view('user.online.scan-qr', [
                    'error' => 'QR code has expired. Please apply again.'
                ]);
            }

            // Assign queue number
            $queueNumber = $this->generateQueueNumber();
            $application->update([
                'entered_queue' => true,
                'queue_number' => $queueNumber,
                'queue_entered_at' => now()
            ]);

            Log::info('Application entered queue via QR', [
                'id' => $application->id,
                'queue_number' => $queueNumber
            ]);

            // Show welcome screen directly
            return view('user.online.welcome', compact('application'));
        } catch (\Exception $e) {
            Log::error('QR scan failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'token' => $token
            ]);

            return view('user.online.scan-qr', [
                'error' => 'Scan failed. Please try again.'
            ]);
        }
    }

    /**
     * API endpoint: Scan QR code, validate token, enter queue, return JSON
     */
    public function handleScanAjax(Request $request)
    {
        $token = $request->input('token');

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'No token provided.'
            ], 400);
        }

        $application = Application::where('qr_token', $token)->first();

        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR code - application not found.'
            ], 404);
        }

        if ($application->entered_queue) {
            return response()->json([
                'success' => false,
                'message' => 'You are already in the queue.',
                'name' => $application->full_name,
                'queue_number' => $application->queue_number
            ], 409);
        }

        if (!$application->isQrValid()) {
            return response()->json([
                'success' => false,
                'message' => 'QR code has expired. Please apply again.'
            ], 410);
        }

        // Enter into queue
        $queueNumber = $this->generateQueueNumber();
        $application->update([
            'entered_queue' => true,
            'queue_number' => $queueNumber,
            'queue_entered_at' => now()
        ]);

        Log::info('QR Scan Success (API)', [
            'application_id' => $application->id,
            'queue_number' => $queueNumber,
            'token' => $token
        ]);

        return response()->json([
    'success' => true,
    'name' => $application->full_name,
    'queue_number' => $queueNumber,
    'service_type' => $application->service_type,
    'is_priority' => $application->isPriority(),
    'priority_type' => $application->getPriorityType(),
    'estimated_wait' => $this->calculateEstimatedWait($application),
    'application_id' => $application->id  // ← Add this line
]);
      
    }
}