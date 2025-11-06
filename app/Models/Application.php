<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Application extends Model
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
        'status',
        'is_preapplied',
        'entered_queue',
        'queue_number',
        'qr_token',
        'qr_expires_at',
        'queue_entered_at', // ✅ Add this
        'completed_at',     // ✅ Add this
        'cancelled_at',     // ✅ Add this
        'pin_code',
        'pin_expires_at',
        
    ];

    protected $casts = [
        'birthdate' => 'date',
        'is_pwd' => 'boolean',
        'is_preapplied' => 'boolean',
        'entered_queue' => 'boolean',
        'age' => 'integer',
        'queue_number' => 'integer',
        'qr_expires_at' => 'datetime',
        'queue_entered_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($application) {
            if (!$application->queue_number) {
                $latest = self::whereDate('created_at', today())
                            ->whereNotNull('queue_number')
                            ->max('queue_number');

                $application->queue_number = ($latest ? $latest : 0) + 1;
            }
        });
    }

    public function getFullNameAttribute()
    {
        return collect([$this->first_name, $this->middle_name, $this->last_name])
            ->filter()
            ->implode(' ');
    }

    public function full_name()
    {
        return $this->getFullNameAttribute();
    }

    /**
     * ✅ FIXED: Check if applicant is priority
     * Must match the logic used in QueueController filtering
     */
    public function isPriority()
    {
        // PWD users are always priority
        if ($this->is_pwd) {
            return true;
        }

        // Users with senior_id (not null and not empty) are priority
        if (!empty(trim($this->senior_id ?? ''))) {
            return true;
        }

        // Senior citizens (60+) are priority even without senior_id
        if ($this->age && $this->age >= 60) {
            return true;
        }

        return false;
    }

    /**
     * ✅ FIXED: Get priority type for display
     */
    public function getPriorityType()
    {
        if ($this->is_pwd) {
            return 'PWD';
        }

        if (!empty(trim($this->senior_id ?? ''))) {
            return 'Senior Citizen';
        }

        if ($this->age && $this->age >= 60) {
            return 'Senior Citizen';
        }

        return 'Regular';
    }

    public function isQrValid()
    {
        return $this->qr_expires_at && $this->qr_expires_at->isFuture();
    }

    public function canEnterQueue()
    {
        return $this->is_preapplied &&
               !$this->entered_queue &&
               $this->isQrValid() &&
               $this->status === 'pending';
    }

    // ✅ SCOPES
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInQueue($query)
    {
        return $query->where('entered_queue', true);
    }

    /**
     * ✅ FIXED: Priority scope - must match isPriority() logic exactly
     * This is the most critical fix!
     */
    public function scopePriority($query)
    {
        return $query->where(function ($q) {
            $q->where('is_pwd', true)  // PWD users
              ->orWhere(function ($subQ) {
                  // Users with valid senior_id
                  $subQ->whereNotNull('senior_id')
                       ->where('senior_id', '!=', '')
                       ->whereRaw("TRIM(senior_id) != ''");
              })
              ->orWhere('age', '>=', 60); // Senior citizens by age
        });
    }

    /**
     * ✅ FIXED: Regular scope - must be opposite of priority
     */
    public function scopeRegular($query)
    {
        return $query->where(function ($q) {
            $q->where('is_pwd', false)  // Not PWD
              ->where(function ($subQ) {
                  // No senior_id or empty senior_id
                  $subQ->whereNull('senior_id')
                       ->orWhere('senior_id', '')
                       ->orWhereRaw("TRIM(senior_id) = ''");
              })
              ->where(function ($subQ) {
                  // Not senior by age (under 60 or null age)
                  $subQ->whereNull('age')
                       ->orWhere('age', '<', 60);
              });
        });
    }

    public function getQueuePosition()
    {
        if (!$this->queue_number) return null;

        return static::where('entered_queue', true)
                    ->where('queue_number', '<', $this->queue_number)
                    ->count() + 1;
    }

    public function getFormattedContactAttribute()
    {
        return $this->contact;
    }

    public function getServiceDisplayNameAttribute()
    {
        $serviceNames = [
            'birth_certificate' => 'Birth Certificate',
            'marriage_certificate' => 'Marriage Certificate',
            'death_certificate' => 'Death Certificate',
            'cenomar' => 'CENOMAR',
        ];

        return $serviceNames[$this->service_type] ?? ucwords(str_replace('_', ' ', $this->service_type));
    }
}