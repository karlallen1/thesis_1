<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailboxSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'contact',
        'birthdate',
        'age',
        'is_pwd',
        'pwd_id',
        'senior_id',
        'service_type',
        'pin_code',
        'pin_expires_at',
        'documents_submitted',
        'submitted_at',
        'mailbox_id',
    ];

    protected $casts = [
        'birthdate' => 'date',
        'is_pwd' => 'boolean',
        'age' => 'integer',
        'pin_expires_at' => 'datetime',
        'documents_submitted' => 'boolean',
        'submitted_at' => 'datetime',
    ];

    public function getFullNameAttribute()
    {
        return collect([$this->first_name, $this->middle_name, $this->last_name])
            ->filter()
            ->implode(' ');
    }
}