<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;

class ApplicationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'contact' => 'required',
            'first_name' => 'required',
            'middle_name' => 'nullable',
            'last_name' => 'required',
            'birthdate' => 'required',
            'age' => 'required|integer',
            'is_pwd' => 'required|in:yes,no',
            'pwd_id' => 'nullable|string',
            'service_type' => 'required|string',
        ]);

        $validated['is_pwd'] = $validated['is_pwd'] === 'yes';

        $application = Application::create($validated);

        return response()->json([
            'success' => true,
            'id' => $application->id,
        ]);
    }
}

