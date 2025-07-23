<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    // ✅ FETCH QUEUE DATA
    public function index()
    {
        $pendingApplicants = Application::where('status', 'pending')
            ->orderBy('created_at')
            ->get();

        $priority = $pendingApplicants->filter(fn($app) => $app->is_pwd || $app->age >= 60)->values();
        $regular = $pendingApplicants->filter(fn($app) => !$app->is_pwd && $app->age < 60)->values();

        $nowServingApp = Application::find(session('now_serving'));

        return response()->json([
            'now_serving' => $nowServingApp ? [
                'id' => $nowServingApp->id,
                'name' => $nowServingApp->first_name . ' ' . $nowServingApp->last_name,
                'queue_number' => $nowServingApp->queue_number,
                'service_type' => $nowServingApp->service_type,
                'is_pwd' => $nowServingApp->is_pwd,
            ] : null,

            'next' => $regular->first()?->id ?? null,
            'cancelled' => session('cancelled_count') ?? 0,

            'priority' => $priority->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->first_name . ' ' . $p->last_name,
                'queue_number' => $p->queue_number,
                'service_type' => $p->service_type,
                'is_pwd' => $p->is_pwd,
            ]),
            'regular' => $regular->map(fn($r) => [
                'id' => $r->id,
                'name' => $r->first_name . ' ' . $r->last_name,
                'queue_number' => $r->queue_number,
                'service_type' => $r->service_type,
            ]),
        ]);
    }

    // ✅ COMPLETE
    public function complete($id)
    {
        $app = Application::findOrFail($id);
        $app->status = 'completed';
        $app->save();

        session(['now_serving' => null]);
        return response()->json(['success' => true]);
    }

    // ✅ CANCEL
    public function cancel($id)
    {
        $app = Application::findOrFail($id);
        $app->status = 'cancelled';
        $app->save();

        session(['now_serving' => null]);
        session(['cancelled_count' => (session('cancelled_count') ?? 0) + 1]);

        return response()->json(['success' => true]);
    }

    // ✅ REQUEUE
    public function requeue($id)
    {
        $app = Application::findOrFail($id);
        $app->touch(); // update created_at
        $app->save();

        session(['now_serving' => null]);
        return response()->json(['success' => true]);
    }

    // ✅ NEXT
    public function next(Request $request)
    {
        $pendingApplicants = Application::where('status', 'pending')
            ->orderBy('created_at')
            ->get();

        $priority = $pendingApplicants->filter(fn($app) => $app->is_pwd || $app->age >= 60)->values();
        $regular = $pendingApplicants->filter(fn($app) => !$app->is_pwd && $app->age < 60)->values();

        $combined = [];
        $r = $p = 0;

        while ($r < $regular->count() || $p < $priority->count()) {
            for ($i = 0; $i < 3 && $r < $regular->count(); $i++) {
                $combined[] = $regular[$r++];
            }
            if ($p < $priority->count()) {
                $combined[] = $priority[$p++];
            }
        }

        if (!empty($combined)) {
            $next = $combined[0];
            session(['now_serving' => $next->id]);
        } else {
            session(['now_serving' => null]);
        }

        return response()->json(['success' => true]);
    }

    // ✅ REQUEUE CURRENT
    public function requeueNow(Request $request)
    {
        $id = session('now_serving');
        if ($id) {
            return $this->requeue($id);
        }
        return response()->json(['success' => false]);
    }

    // ✅ AUTO-ASSIGN QUEUE NUMBER
    public function assignQueueNumber(Application $application)
    {
        if (!$application->queue_number) {
            $latest = Application::whereNotNull('queue_number')
                ->orderByDesc('created_at')
                ->first();

            $lastNumber = $latest ? intval(preg_replace('/\D/', '', $latest->queue_number)) : 0;
            $newNumber = $lastNumber + 1;
            $application->queue_number = 'Q' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
            $application->save();
        }

        return response()->json(['queue_number' => $application->queue_number]);
    }

    // ✅ CREATE APPLICATION
    public function store(Request $request)
    {
        $data = $request->all();
        $data['status'] = 'pending';

        $application = Application::create($data);
        $this->assignQueueNumber($application);

        return response()->json(['success' => true, 'application' => $application]);
    }
}
