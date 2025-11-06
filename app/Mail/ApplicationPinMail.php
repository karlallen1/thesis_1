<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApplicationPinMail extends Mailable
{
    use Queueable, SerializesModels;

    public $applicant;
    public $pinCode;
    public $qrFilename;

    public function __construct($applicant, $pinCode, $qrFilename)
    {
        $this->applicant = $applicant;
        $this->pinCode = $pinCode;
        $this->qrFilename = $qrFilename;
    }

    public function build()
    {
        $qrPath = storage_path("app/public/qrcodes/{$this->qrFilename}");

        if (!file_exists($qrPath)) {
            throw new \Exception("QR code file not found: {$qrPath}");
        }

        return $this->subject('Application Received - Your PIN Code & QR Code')
            ->view('emails.application-pin-qr')
            ->with([
                'applicant' => $this->applicant,
                'pinCode' => $this->pinCode,
                'qrPath' => $qrPath,
            ])
            ->attachData(
                file_get_contents($qrPath),
                "ApplicationQRCode-{$this->applicant->id}.png",
                ['mime' => 'image/png']
            );
    }
}