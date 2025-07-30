<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ApplicationSubmittedMail;
use Illuminate\Support\Str;
use Carbon\Carbon;

// ✅ Correct Endroid v5+ imports
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
// ✅ Fix for v5.x: Import the Enums, not the non-existent classes
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
// Remove or comment out the old incorrect imports:
// use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
// use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;

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
                    'regex:/^[\w\.-]+@(gmail\.com|yahoo\.com)$/'
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
            $qrExpiresAt = now()->addHours(36);

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
            ]);

            Log::info('Application saved to DB', [
                'id' => $application->id,
                'qr_expires_at' => $qrExpiresAt->toDateTimeString()
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
                'message' => 'Application submitted! Check your email for the QR code.',
                'application_id' => $application->id
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
}
