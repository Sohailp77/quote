<?php

namespace App\Http\Controllers;

use App\Models\Revenue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RevenueController extends Controller
{
    /**
     * Remove the specified revenue record.
     */
    public function destroy(Revenue $revenue)
    {
        Gate::authorize('boss');

        // If it's linked to a quote, we might want to warn or prevent 
        // but user specifically asked for delete/edit/revert.
        // For now, allow deletion if authorized as requested.
        $revenue->delete();

        return back()->with('success', 'Revenue record deleted.');
    }

    /**
     * Update a revenue record (boss only).
     */
    public function update(Request $request, Revenue $revenue)
    {
        Gate::authorize('boss');

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        $revenue->update([
            'amount' => (float) $validated['amount'],
        ]);

        return back()->with('success', 'Revenue record updated.');
    }
}
