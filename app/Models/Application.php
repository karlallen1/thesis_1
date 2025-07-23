<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'email',
        'contact',
        'first_name',
        'middle_name',
        'last_name',
        'birthdate',
        'age',
        'is_pwd',
        'pwd_id',
        'service_type',
        'status',         // ✅ already added
        'queue_number'    // ✅ ADD THIS
    ];

    protected static function boot()
{
    parent::boot();

    static::creating(function ($application) {
        if (!$application->queue_number) {
            $latest = self::whereNotNull('queue_number')
                ->orderByDesc('created_at')
                ->first();

            $lastNumber = $latest ? intval(preg_replace('/\D/', '', $latest->queue_number)) : 0;
            $newNumber = $lastNumber + 1;
            $application->queue_number = 'Q' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        }
    });
}

}
