<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MailboxSubmission;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\MailboxPinMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MailboxController extends Controller
{
    /**
     * Handle new mailbox submission (from /prevalidate form)
     */
    public function store(Request $request)
    {
        try {
            Log::info('Starting mailbox submission', ['email' => $request->email]);

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
                'birthdate' => 'required|date',
                'is_pwd' => 'nullable|in:yes,no',
                'pwd_id' => 'nullable|string|max:50',
                'senior_id' => 'nullable|string|max:50',
                'service_type' => 'required|string|max:255'
            ]);

            $age = Carbon::parse($request->birthdate)->age;

            // Generate unique 6-digit PIN
            do {
                $pinCode = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
            } while (MailboxSubmission::where('pin_code', $pinCode)->exists());

            $pinExpiresAt = now()->addHours(24);

            $submission = MailboxSubmission::create([
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
                'pin_code' => $pinCode,
                'pin_expires_at' => $pinExpiresAt,
            ]);

            Log::info('Mailbox submission saved', [
                'id' => $submission->id,
                'pin_code' => $pinCode,
            ]);

            try {
                Mail::to($submission->email)->send(new MailboxPinMail($submission, $pinCode, $pinExpiresAt));
                Log::info('PIN email sent', ['email' => $submission->email]);
            } catch (\Exception $mailException) {
                Log::error('Email failed', ['error' => $mailException->getMessage()]);
                
                return response()->json([
                    'success' => true,
                    'warning' => 'Submission saved but email failed.',
                    'pin_code' => $pinCode
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Submission successful! Check your email for PIN.',
                'submission_id' => $submission->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Mailbox submission failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Submission failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Validate PIN from IoT device (ESP32)
     */
    public function validatePin(Request $request)
    {
        $request->validate([
            'pin_code' => 'required|string|size:6'
        ]);

        $pin = $request->pin_code;

        // Find active submission
        $submission = MailboxSubmission::where('pin_code', $pin)
            ->where('pin_expires_at', '>', now())
            ->whereNull('mailbox_used_at') // not yet used
            ->first();

        if (!$submission) {
            return response()->json([
                'valid' => false,
                'error' => 'Invalid, expired, or already used PIN.'
            ], 404);
        }

        // Mark as used
        $submission->update([
            'mailbox_used_at' => now(),
            'documents_submitted' => 1,
        ]);

        Log::info('PIN validated and mailbox opened', [
            'submission_id' => $submission->id,
            'pin_code' => $pin,
            'name' => $submission->first_name . ' ' . $submission->last_name
        ]);

        return response()->json([
            'valid' => true,
            'name' => trim("$submission->first_name $submission->middle_name $submission->last_name"),
            'service_type' => $submission->service_type,
            'open_duration' => 5000 // unlock for 5 seconds
        ]);
    }

    /**
     * Get submissions for Admin MAILBOX Tab
     */
    public function getMailboxSubmissions()
    {
        $submissions = MailboxSubmission::whereNotNull('mailbox_used_at')
            ->orderByDesc('mailbox_used_at')
            ->get()
            ->map(function ($s) {
                $fullName = trim("$s->first_name " . ($s->middle_name ? "$s->middle_name " : "") . "$s->last_name");
                return [
                    'id' => $s->id,
                    'full_name' => $fullName,
                    'email' => $s->email,
                    'contact' => $s->contact,
                    'service_type' => $s->service_type,
                    'pin_code' => $s->pin_code,
                    'used_at' => $s->mailbox_used_at,
                    'admin_status' => $s->admin_status,
                ];
            });

        return response()->json($submissions);
    }

    /**
     * Approve document submission (Admin action)
     */
    public function approveSubmission($id)
    {
        $submission = MailboxSubmission::findOrFail($id);

        $submission->update(['admin_status' => 'approved']);

        Log::info('Admin approved mailbox submission', [
            'submission_id' => $id,
            'admin' => Auth::check() ? Auth::user()->username : 'unknown'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Requirements approved successfully.'
        ]);
    }

    /**
     * Disapprove document submission (Admin action)
     */
    public function disapproveSubmission($id)
    {
        $submission = MailboxSubmission::findOrFail($id);

        $submission->update(['admin_status' => 'disapproved']);

        Log::info('Admin disapproved mailbox submission', [
            'submission_id' => $id,
            'admin' => Auth::check() ? Auth::user()->username: 'unknown'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Requirements disapproved.'
        ]);
    }
}