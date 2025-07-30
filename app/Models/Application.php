<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Application extends Model
{
    use HasFactory;

    /**
     * ✅ Updated fillable fields - includes all new QR and queue fields
     */
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
        'status',
        'is_preapplied',
        'entered_queue',
        'queue_number',
        'qr_token',           // ✅ NEW: For QR code security
        'qr_expires_at',      // ✅ NEW: QR expiration timestamp
        'queue_entered_at',   // ✅ NEW: When user entered physical queue
        'name'                // ✅ Keep your existing 'name' field
    ];

    /**
     * ✅ Proper casting for all field types
     */
    protected $casts = [
        'birthdate' => 'date',
        'is_pwd' => 'boolean',
        'is_preapplied' => 'boolean', 
        'entered_queue' => 'boolean',
        'age' => 'integer',
        'queue_number' => 'integer',
        'qr_expires_at' => 'datetime',    // ✅ NEW: Cast QR expiration
        'queue_entered_at' => 'datetime', // ✅ NEW: Cast queue entry time
    ];

    /**
     * ✅ Helper method for full name - used by QueueController
     */
    public function getFullNameAttribute()
    {
        $parts = array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name
        ]);
        
        return implode(' ', $parts);
    }

    /**
     * ✅ Alternative method name that your controller might be calling
     */
    public function full_name()
    {
        return $this->getFullNameAttribute();
    }

    /**
     * ✅ UPDATED: Priority checker - PWD and those with Senior ID get priority
     * Only PWDs or Seniors who provide a senior_id are priority.
     * Age alone (even 60+) does not grant priority.
     */
    public function isPriority()
    {
        // PWD has priority
        if ($this->is_pwd) {
            return true;
        }
        
        // Senior Citizen has priority ONLY if they have provided a senior_id
        // This correctly handles null, empty string, or whitespace
        if (!empty($this->senior_id) && is_string($this->senior_id) && trim($this->senior_id) !== '') {
            return true;
        }
        
        return false;
    }

    /**
     * ✅ UPDATED: Get priority type for display
     */
    public function getPriorityType()
    {
        if ($this->is_pwd) {
            return 'PWD';
        }
        
        if (!empty($this->senior_id) && is_string($this->senior_id) && trim($this->senior_id) !== '') {
            return 'Senior Citizen';
        }
        
        return null;
    }

    /**
     * ✅ Check if QR code is still valid
     */
    public function isQrValid()
    {
        return $this->qr_expires_at && $this->qr_expires_at->isFuture();
    }

    /**
     * ✅ Check if application can enter queue
     */
    public function canEnterQueue()
    {
        return $this->is_preapplied && 
               !$this->entered_queue && 
               $this->isQrValid() &&
               $this->status === 'pending';
    }

    /**
     * ✅ Scope for pending applications
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * ✅ Scope for applications in queue
     */
    public function scopeInQueue($query)
    {
        return $query->where('entered_queue', true);
    }

    /**
     * ✅ FIXED: Scope for priority applications
     * Consistent with isPriority() logic:
     * Applications that are either PWD OR have a non-empty senior_id
     */
    public function scopePriority($query)
    {
        return $query->where(function($q) {
            $q->where('is_pwd', true)
              ->orWhere(function($q2) {
                 // Senior: is_pwd is false AND senior_id is provided (not null, not empty string)
                 $q2->where('is_pwd', false) // Exclude PWDs as they are handled by the first condition
                    ->whereNotNull('senior_id')
                    ->where('senior_id', '!=', '')
                    ->where('senior_id', '!=', 'NULL'); // Extra safety check
              });
        });
    }

    /**
     * ✅ NEW: Scope for regular applications - NOT priority (opposite of priority)
     * Applications that are neither PWD nor have a senior_id
     */
    public function scopeRegular($query)
    {
        return $query->where(function($q) {
            $q->where('is_pwd', false)
              ->where(function($subQ) {
                  $subQ->whereNull('senior_id')
                       ->orWhere('senior_id', '')
                       ->orWhere('senior_id', 'NULL'); // Match the safety check in scopePriority
              });
        });
    }

    /**
     * ✅ Get queue position relative to others
     */
    public function getQueuePosition()
    {
        if (!$this->queue_number) {
            return null;
        }

        $position = static::where('entered_queue', true)
            ->where('queue_number', '<', $this->queue_number)
            ->count() + 1;

        return $position;
    }

    /**
     * ✅ Format contact number for display
     */
    public function getFormattedContactAttribute()
    {
        return $this->contact;
    }

    /**
     * ✅ Get service type display name
     */
    public function getServiceDisplayNameAttribute()
    {
        $serviceNames = [
            'birth_certificate' => 'Birth Certificate',
            'marriage_certificate' => 'Marriage Certificate', 
            'death_certificate' => 'Death Certificate',
            'cenomar' => 'CENOMAR'
        ];

        return $serviceNames[$this->service_type] ?? ucwords(str_replace('_', ' ', $this->service_type));
    }
}