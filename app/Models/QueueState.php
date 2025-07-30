<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QueueState extends Model
{
    protected $fillable = ['key', 'value'];

    protected $casts = [
        'value' => 'json'
    ];

    /**
     * Get a queue state value
     */
    public static function getValue($key, $default = null)
    {
        $state = static::where('key', $key)->first();
        return $state ? $state->value : $default;
    }

    /**
     * Set a queue state value
     */
    public static function setValue($key, $value)
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Get current serving application
     */
    public static function getNowServing()
    {
        $id = static::getValue('now_serving');
        return $id ? \App\Models\Application::find($id) : null;
    }

    /**
     * Set currently serving application
     */
    public static function setNowServing($applicationId)
    {
        static::setValue('now_serving', $applicationId);
    }

    /**
     * Clear currently serving
     */
    public static function clearNowServing()
    {
        static::setValue('now_serving', null);
    }
}