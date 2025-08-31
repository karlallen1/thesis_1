<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\QueueState;
use App\Models\QueueCounter; // ✅ For atomic queue number
use Illuminate\Support\Facades\DB; // ✅ For transaction safety
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class QueueController extends Controller
{
    /**
     * ✅ KIOSK STORE METHOD - Direct queue entry without QR/Email
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

            // ✅ Always return JSON for kiosk
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
     * ✅ QR SCAN ENTRY - For pre-registered users
     */
    public function enterViaQR(Request $request)
    {
        try {
            $token = $request->get('token');
            if (!$token) {
                Log::warning('QR scan failed - no token provided');
                return view('queue.scan', [
                    'success' => false,
                    'message' => 'Invalid QR code - no token provided.',
                    'success_message' => 'Invalid QR code - no token provided.',
                    'application' => null
                ]);
            }

            $application = Application::where('qr_token', $token)->first();
            if (!$application) {
                Log::warning('QR scan failed - application not found', ['token' => $token]);
                return view('queue.scan', [
                    'success' => false,
                    'message' => 'Invalid QR code - application not found.',
                    'success_message' => 'Invalid QR code - application not found.',
                    'application' => null
                ]);
            }

            if ($application->entered_queue) {
                Log::info('QR scan - already in queue', ['application_id' => $application->id]);
                return view('queue.scan', [
                    'success' => false,
                    'message' => 'You are already in the queue.',
                    'success_message' => 'You are already in the queue.',
                    'application' => $application,
                    'queue_number' => $application->queue_number
                ]);
            }

            if (!$application->isQrValid()) {
                Log::warning('QR scan failed - expired', [
                    'application_id' => $application->id,
                    'expires_at' => $application->qr_expires_at
                ]);
                return view('queue.scan', [
                    'success' => false,
                    'message' => 'QR code has expired. Please submit a new application.',
                    'success_message' => 'QR code has expired. Please submit a new application.',
                    'application' => $application
                ]);
            }

            $queueNumber = $this->generateQueueNumber(); // ✅ Atomic
            $application->update([
                'entered_queue' => true,
                'queue_number' => $queueNumber,
                'queue_entered_at' => now()
            ]);

            Log::info('QR scan successful - entered queue', [
                'application_id' => $application->id,
                'queue_number' => $queueNumber
            ]);

            $this->printQueueTicket($application);

            return view('queue.scan', [
                'success' => true,
                'message' => 'Successfully entered the queue! Your ticket is printing.',
                'success_message' => 'Successfully entered the queue! Your ticket is printing.',
                'application' => $application,
                'queue_number' => $queueNumber,
                'applicant_name' => $application->full_name,
                'service_type' => $application->service_type,
                'is_priority' => $application->isPriority(),
                'priority_type' => $application->getPriorityType(),
                'estimated_wait' => $this->calculateEstimatedWait($application)
            ]);
        } catch (\Exception $e) {
            Log::error('QR scan entry failed', [
                'error' => $e->getMessage(),
                'token' => $token
            ]);
            return view('queue.scan', [
                'success' => false,
                'message' => 'QR scan failed. Please try again.',
                'success_message' => 'QR scan failed. Please try again.',
                'application' => null
            ]);
        }
    }

    /**
     * ✅ MAIN FIX: Get current queue status with proper priority filtering
     */
    public function index(Request $request)
    {
        try {
            // Get all pending applications in queue
            $query = Application::where('entered_queue', true)
                ->where('status', 'pending')
                ->orderBy('queue_number');

            if ($request->has('service_type') && $request->service_type) {
                $query->where('service_type', $request->service_type);
            }

            $allApplications = $query->get();
            
            // ✅ DEBUG: Log all applications for troubleshooting
            Log::debug('All applications in queue:', [
                'count' => $allApplications->count(),
                'applications' => $allApplications->map(function ($app) {
                    return [
                        'id' => $app->id,
                        'queue_number' => $app->queue_number,
                        'name' => $app->full_name,
                        'is_pwd' => $app->is_pwd,
                        'senior_id' => $app->senior_id,
                        'age' => $app->age,
                        'isPriority_method' => $app->isPriority(),
                        'priority_type' => $app->getPriorityType(),
                    ];
                })->toArray()
            ]);

            // ✅ CRITICAL FIX: Use the model's isPriority() method instead of scopes
            $priorityApplications = $allApplications->filter(function ($app) {
                return $app->isPriority();
            });

            $regularApplications = $allApplications->filter(function ($app) {
                return !$app->isPriority();
            });

            $nowServingApp = QueueState::getNowServing();

            $cancelledCount = Application::where('entered_queue', true)
                ->where('status', 'cancelled')
                ->whereDate('updated_at', today())
                ->count();

            // Format priority applications
            $formattedPriority = $priorityApplications->map(function ($app) {
                return [
                    'id' => $app->id,
                    'queue_number' => $app->queue_number,
                    'name' => $app->full_name,
                    'service_type' => $app->service_type,
                    'is_pwd' => $app->is_pwd,
                    'senior_id' => $app->senior_id,
                    'age' => $app->age,
                    'priority_type' => $app->getPriorityType(),
                ];
            })->values(); // ✅ Reset array keys

            // Format regular applications
            $formattedRegular = $regularApplications->map(function ($app) {
                return [
                    'id' => $app->id,
                    'queue_number' => $app->queue_number,
                    'name' => $app->full_name,
                    'service_type' => $app->service_type,
                ];
            })->values(); // ✅ Reset array keys

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

            $response = [
                'now_serving' => $nowServingData,
                'priority' => $formattedPriority,
                'regular' => $formattedRegular,
                'cancelled' => $cancelledCount,
            ];

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('Queue index failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'now_serving' => null,
                'priority' => [],
                'regular' => [],
                'cancelled' => 0,
            ], 500);
        }
    }

    /**
     * ✅ Public Queue Display Data - Fixed for priority
     */
    public function displayData(Request $request)
    {
        try {
            $applications = Application::where('entered_queue', true)
                ->where('status', 'pending')
                ->orderBy('queue_number')
                ->get();

            // ✅ Use consistent filtering
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

            $nextPriority = $priority->map(function ($app) {
                return [
                    'queue_number' => $app->queue_number,
                    'name' => $app->full_name,
                    'service_type' => $app->service_type,
                    'is_priority' => true,
                    'priority_type' => $app->getPriorityType(),
                ];
            })->values();

            $nextRegular = $regular->map(function ($app) {
                return [
                    'queue_number' => $app->queue_number,
                    'name' => $app->full_name,
                    'service_type' => $app->service_type,
                    'is_priority' => false,
                ];
            })->values();

            return response()->json([
                'now_serving' => $formattedNowServing,
                'priority' => $nextPriority,
                'regular' => $nextRegular,
            ]);
        } catch (\Exception $e) {
            Log::error('Public queue display data failed', [
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'now_serving' => null,
                'priority' => [],
                'regular' => [],
            ], 500);
        }
    }

    /**
     * ✅ IMPROVED: Call next person with consistent priority logic
     */
    public function next(Request $request)
    {
        try {
            // Get all pending applications
            $applications = Application::where('entered_queue', true)
                ->where('status', 'pending')
                ->orderBy('queue_number')
                ->get();

            // ✅ Use consistent filtering logic
            $priorityApps = $applications->filter(fn($app) => $app->isPriority());
            $regularApps = $applications->filter(fn($app) => !$app->isPriority());

            $nextApplication = null;

            // Priority first, then regular
            if ($priorityApps->isNotEmpty()) {
                $nextApplication = $priorityApps->sortBy('queue_number')->first();
            } elseif ($regularApps->isNotEmpty()) {
                $nextApplication = $regularApps->sortBy('queue_number')->first();
            }

            if (!$nextApplication) {
                return response()->json([
                    'success' => false,
                    'message' => 'No one in queue.'
                ]);
            }

            // Set as now serving
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
            Log::error('Next queue failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to call next person.'
            ], 500);
        }
    }

    /**
     * ✅ Complete application by ID
     */
        public function complete(Request $request, $id)
    {
        try {
            $application = Application::findOrFail($id);
            $application->update([
                'status' => 'completed',
                'completed_at' => now() // ✅ Add this
            ]);

            if (QueueState::getNowServing()?->id == $id) {
                QueueState::clearNowServing();
            }

            Log::info('Application completed', ['application_id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Service completed successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Complete application failed', ['error' => $e->getMessage(), 'id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete service.'
            ], 500);
        }
    }

    /**
     * ✅ Cancel application by ID
     */
    public function cancel(Request $request, $id)
    {
        try {
            $application = Application::findOrFail($id);
            $application->update([
                'status' => 'cancelled',
                'cancelled_at' => now() // ✅ Add this
            ]);

            if (QueueState::getNowServing()?->id == $id) {
                QueueState::clearNowServing();
            }

            Log::info('Application cancelled', ['application_id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Application cancelled successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Cancel application failed', ['error' => $e->getMessage(), 'id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel application.'
            ], 500);
        }
    }

    /**
     * ✅ Requeue application by ID
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

            return response()->json([
                'success' => true,
                'message' => 'Application requeued successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Requeue application failed', ['error' => $e->getMessage(), 'id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to requeue application.'
            ], 500);
        }
    }

    /**
     * ✅ Complete currently serving
     */
    public function completeNow(Request $request)
    {
        $nowServing = QueueState::getNowServing();
        if (!$nowServing) {
            return response()->json([
                'success' => false,
                'message' => 'No one is currently being served.'
            ]);
        }
        return $this->complete($request, $nowServing->id);
    }

    /**
     * ✅ Cancel currently serving
     */
    public function cancelNow(Request $request)
    {
        $nowServing = QueueState::getNowServing();
        if (!$nowServing) {
            return response()->json([
                'success' => false,
                'message' => 'No one is currently being served.'
            ]);
        }
        return $this->cancel($request, $nowServing->id);
    }

    /**
     * ✅ Requeue currently serving
     */
    public function requeueNow(Request $request)
    {
        $nowServing = QueueState::getNowServing();
        if (!$nowServing) {
            return response()->json([
                'success' => false,
                'message' => 'No one is currently being served.'
            ]);
        }
        QueueState::clearNowServing();
        return $this->requeue($request, $nowServing->id);
    }

    /**
     * ✅ Generate next queue number (atomic & daily reset)
     */
    private function generateQueueNumber()
    {
        return DB::transaction(function () {
            // Step 1: Get or create today's counter
            $counter = QueueCounter::firstOrCreate(['date' => today()], ['counter' => 0]);

            // Step 2: Re-fetch with row lock (this executes the lock)
            $locked = QueueCounter::where('id', $counter->id)
                ->lockForUpdate()
                ->first();

            if (! $locked) {
                throw new \Exception('Queue counter not found.');
            }

            // Step 3: Increment safely
            $locked->increment('counter');

            return $locked->counter;
        });
    }

    /**
     * ✅ Calculate estimated wait time
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
     * ✅ Print queue ticket (placeholder)
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
}