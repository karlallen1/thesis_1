<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class QueueCounter extends Model
{
    protected $fillable = ['date', 'counter'];
    protected $casts = ['date' => 'date'];

    /**
     * Get or create today's counter
     */
    public static function today()
    {
        return static::firstOrCreate(['date' => today()], ['counter' => 0]);
    }

    /**
     * Atomically increment and return next number
     */
    public function getNextNumber()
    {
        return DB::transaction(function () {
            // Re-fetch the same record with a row lock
            $locked = static::where('id', $this->id)
                ->lockForUpdate()
                ->first();

            if (! $locked) {
                throw new \Exception('Queue counter not found.');
            }

            $locked->increment('counter');
            return $locked->counter;
        });
    }
}