<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SystemLog extends Model
{
    protected $fillable = ['type', 'user', 'action', 'details', 'status', 'created_at'];

    protected $casts = [
        'details' => 'array',
        'created_at' => 'datetime',
    ];

    public static function log($type, $action, $details = null, $status = 'SUCCESS')
    {
        self::create([
            'type' => strtoupper($type),
            'user' => Auth::check() ? Auth::user()->name : 'System',
            'action' => $action,
            'details' => is_null($details) ? null : json_encode($details),
            'status' => $status,
        ]);
    }
}