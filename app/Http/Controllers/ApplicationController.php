<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ApplicationSubmittedMail;
use Illuminate\Support\Str;
use Carbon\Carbon;


use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;


class ApplicationController extends Controller
{
    public function store(Request $request)
    {
        try {
            Log::info('Starting application submission', ['email' => $request->email]);
            Log::info('Submitted birthdate', ['birthdate' => $request->input('birthdate')]);

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
                'email.regex' => 'Email must be from gmail.com or yahoo.com',
                'contact.regex' => 'Contact must be in +63 format with 10 digits',
                'birthdate.before_or_equal' => 'Sorry, you did not meet age requirements',
                'birthdate.after' => 'Invalid birthdate'
            ]);

            Log::info('Validation passed, calculating age and saving to database');

            $age = Carbon::parse($request->birthdate)->age;
            $qrToken = Str::random(32);
            
            //pinthingys
            $pinCode = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

            // ✅ UPDATED: Changed from 36 hours to 24 hours
            $qrExpiresAt = now()->addHours(24);

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
                'is_preapplied' => true,
                'entered_queue' => false,
                'qr_token' => $qrToken,
                'qr_expires_at' => $qrExpiresAt,
                'pin_code' => $pinCode,//pinadd
            ]);

            Log::info('Application saved to DB with 24-hour QR validity', [
                'id' => $application->id,
                'qr_expires_at' => $qrExpiresAt->toDateTimeString(),
                'validity_hours' => 24,
                'created_at' => $application->created_at->toDateTimeString()
            ]);

            $qrUrl = route('queue.scan', ['token' => $qrToken]);
            Log::info('Generating QR code with secure URL', ['url' => $qrUrl]);

            // ✅ Create QR Code with Endroid v5
            $qrDirectory = storage_path('app/public/qrcodes');
            if (!file_exists($qrDirectory)) {
                mkdir($qrDirectory, 0755, true);
            }

            $filename = "qr_{$application->id}_{$qrToken}.png";
            $fullPath = "{$qrDirectory}/{$filename}";

            // ✅ Fix for v5.x: Use Enum values directly
            $result = Builder::create()
                ->writer(new PngWriter())
                ->data($qrUrl)
                ->encoding(new Encoding('UTF-8'))
                // ✅ Change here: Use ErrorCorrectionLevel Enum
                ->errorCorrectionLevel(ErrorCorrectionLevel::High)
                ->size(300)
                ->margin(10)
                // ✅ Change here: Use RoundBlockSizeMode Enum
                ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
                ->build();

            file_put_contents($fullPath, $result->getString());

            Log::info('QR code saved successfully', ['path' => $fullPath]);

            try {
                Mail::to($application->email)->send(new ApplicationSubmittedMail($application, $filename));
                Log::info('Email sent successfully', ['email' => $application->email]);
            } catch (\Exception $mailException) {
                Log::error('Email sending failed', [
                    'error' => $mailException->getMessage(),
                    'application_id' => $application->id
                ]);

                return response()->json([
                    'success' => true,
                    'warning' => 'Application submitted, but email delivery failed. Please contact support.'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Application submitted! Check your email for the QR code (valid for 24 hours).',
                'application_id' => $application->id,
                'qr_expires_at' => $qrExpiresAt->toDateTimeString(),
                'validity_hours' => 24
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation failed', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Application submission failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Application submission failed. Please try again.'
            ], 500);
        }
    }

    /**
     * ✅ NEW: Create test QR with custom expiry - FOR TESTING PURPOSES ONLY
     */
    public function createTestQr(Request $request)
    {
        // Only allow in local/testing environment
        if (!app()->environment(['local', 'testing'])) {
            abort(404);
        }

        try {
            $expiryMinutes = $request->get('expiry_minutes', 1); // Default 1 minute for quick testing
            $qrToken = Str::random(32);
            $qrExpiresAt = now()->addMinutes($expiryMinutes);

            $application = Application::create([
                'first_name' => 'Test',
                'middle_name' => '',
                'last_name' => 'User',
                'email' => 'test@gmail.com',
                'contact' => '+63 912 345 6789',
                'birthdate' => '1990-01-01',
                'age' => 34,
                'is_pwd' => false,
                'pwd_id' => null,
                'senior_id' => null,
                'service_type' => 'Test Service',
                'status' => 'pending',
                'is_preapplied' => true,
                'entered_queue' => false,
                'qr_token' => $qrToken,
                'qr_expires_at' => $qrExpiresAt,
            ]);

            Log::info('Test QR application created', [
                'id' => $application->id,
                'token' => $qrToken,
                'expires_at' => $qrExpiresAt->toDateTimeString(),
                'expiry_minutes' => $expiryMinutes
            ]);

            return response()->json([
                'success' => true,
                'message' => "Test QR created (expires in {$expiryMinutes} minutes)",
                'application_id' => $application->id,
                'token' => $qrToken,
                'qr_expires_at' => $qrExpiresAt->toDateTimeString(),
                'scan_url' => route('queue.scan', ['token' => $qrToken]),
                'test_url' => route('queue.test-expiry', ['token' => $qrToken]),
                'current_time' => now()->toDateTimeString(),
                'expires_in_minutes' => $expiryMinutes
            ]);

        } catch (\Exception $e) {
            Log::error('Test QR creation failed', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Test QR creation failed: ' . $e->getMessage()
            ], 500);
        }
    }
}