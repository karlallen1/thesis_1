<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ApplicationSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $applicant;
    public $qrFilename;
    public $embeddedQrPath;

    public function __construct($applicant, $qrFilename)
    {
        $this->applicant = $applicant;
        $this->qrFilename = $qrFilename;
    }

    public function build()
    {
        $qrPath = storage_path("app/public/qrcodes/{$this->qrFilename}");

        if (!file_exists($qrPath)) {
            Log::error('QR code file not found for email attachment', [
                'path' => $qrPath,
                'application_id' => $this->applicant->id,
                'email' => $this->applicant->email
            ]);
            throw new \Exception("QR code file not found: {$qrPath}");
        }

        return $this->subject('Application Received - Tax Declaration')
            ->view('emails.application-submitted')
            ->with([
                'applicant' => $this->applicant,
                'qrPath' => $qrPath,
            ])
            ->attachData(
                file_get_contents($qrPath),
                "ApplicationQRCode-{$this->applicant->id}.png",
                ['mime' => 'image/png']
            );
    }
}
