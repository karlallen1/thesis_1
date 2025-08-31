<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemLog;
use Illuminate\Http\Request;

class SystemLogsController extends Controller
{
    public function getLogs(Request $request)
    {
        $query = SystemLog::query();

        // Apply type filter (but only for account-related logs as per requirements)
        if ($request->filled('type')) {
            $typeMap = [
                'account' => 'ACCOUNT',
                'queue' => 'QUEUE',
                'system' => 'SYSTEM',
                'daily_summary' => 'DAILY_SUMMARY'
            ];
            $type = $typeMap[strtolower($request->type)] ?? strtoupper($request->type);
            
            // Only filter by type if it's an account-related type
            if ($request->type === 'account') {
                $query->where(function($q) {
                    $q->whereIn('action', [
                        'ACCOUNT_CREATE',
                        'ACCOUNT_DELETE', 
                        'PASSWORD_CHANGE'
                    ])
                    ->orWhere('type', 'ACCOUNT');
                });
            } else {
                $query->where('type', $type);
            }
        }

        // Apply single date filter instead of date range
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Only show account-related logs by default (unless specifically requested)
        if (!$request->filled('type') || $request->type === 'all') {
            $query->where(function($q) {
                $q->whereIn('action', [
                    'ACCOUNT_CREATE',
                    'ACCOUNT_DELETE', 
                    'PASSWORD_CHANGE'
                ])
                ->orWhere('type', 'ACCOUNT');
            });
        }

        $logs = $query->latest()->get();

        return response()->json($logs->map(function ($log) {
            return [
                'id' => $log->id,
                'date' => $log->created_at,
                'type' => $log->type,
                'user' => $log->user,
                'action' => $log->action,
                'details' => $log->details,
                'status' => $log->status,
            ];
        }));
    }
}