<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SmtpConfigurationController extends Controller
{
    public function index()
    {
        $configs = \App\Models\SmtpConfiguration::orderBy('priority')->get();
        return view('admin.smtp.index', compact('configs'));
    }

    public function create()
    {
        return view('admin.smtp.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'host' => 'required|string',
            'port' => 'required|integer',
            'username' => 'nullable|string',
            'password' => 'nullable|string',
            'encryption' => 'nullable|string',
            'from_address' => 'required|email',
            'from_name' => 'required|string',
            'priority' => 'required|integer',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        \App\Models\SmtpConfiguration::create($validated);

        return redirect()->route('admin.smtp.index')->with('success', 'SMTP configuration created.');
    }

    public function edit(\App\Models\SmtpConfiguration $smtp)
    {
        return view('admin.smtp.edit', compact('smtp'));
    }

    public function update(Request $request, \App\Models\SmtpConfiguration $smtp)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'host' => 'required|string',
            'port' => 'required|integer',
            'username' => 'nullable|string',
            'password' => 'nullable|string',
            'encryption' => 'nullable|string',
            'from_address' => 'required|email',
            'from_name' => 'required|string',
            'priority' => 'required|integer',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $smtp->update($validated);

        return redirect()->route('admin.smtp.index')->with('success', 'SMTP configuration updated.');
    }

    public function destroy(\App\Models\SmtpConfiguration $smtp)
    {
        $smtp->delete();
        return redirect()->route('admin.smtp.index')->with('success', 'SMTP configuration deleted.');
    }
}
