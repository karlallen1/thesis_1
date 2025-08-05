<?php

namespace App\Http\Controllers\Admin;

// ✅ Import the correct base Controller from the main namespace
use App\Http\Controllers\Controller;
use App\Models\SystemLog;
use Illuminate\Http\Request;

class SystemLogsController extends Controller
{
    public function getLogs(Request $request)
    {
        $query = SystemLog::query();

        if ($request->filled('type')) {
            $typeMap = [
                'account' => 'ACCOUNT',
                'queue' => 'QUEUE',
                'system' => 'SYSTEM',
                'daily_summary' => 'DAILY_SUMMARY'
            ];
            $type = $typeMap[strtolower($request->type)] ?? strtoupper($request->type);
            $query->where('type', $type);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
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