<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailboxPinMail extends Mailable
{
    use Queueable, SerializesModels;

    public $submission;
    public $pinCode;
    public $expiresAt;
    public $requirements;
    public $note;

    public function __construct($submission, $pinCode, $expiresAt)
    {
        $this->submission = $submission;
        $this->pinCode = $pinCode;
        $this->expiresAt = $expiresAt;
        
        // Set requirements based on service type
        $this->requirements = $this->getRequirements($submission->service_type);
        $this->note = $this->getNote($submission->service_type);
    }

    private function getRequirements($serviceType)
    {
        $requirements = [
            'No Improvement' => [
                'Tax Declaration Receipt',
                'Valid ID'
            ],
            'Business Permit' => [
                'DTI Registration',
                'Barangay Clearance',
                'Valid ID',
                'Latest Tax Payment'
            ],
            'Barangay Clearance' => [
                'Valid ID',
                'Proof of Residency',
                'Purpose Letter'
            ],
            'Indigency Certification' => [
                'Valid ID',
                'Proof of Indigency',
                'Purpose Letter'
            ]
        ];

        return $requirements[$serviceType] ?? [];
    }

    private function getNote($serviceType)
    {
        $notes = [
            'No Improvement' => 'Bring original and photocopy of all documents.',
            'Business Permit' => 'Application must be renewed annually.',
            'Barangay Clearance' => 'Valid for local transactions only.',
            'Indigency Certification' => 'Valid for 30 days from issuance.'
        ];

        return $notes[$serviceType] ?? '';
    }

    public function build()
    {
        return $this->subject('Your Mailbox PIN - North Caloocan City Hall')
            ->view('emails.mailbox-pin');
    }
}