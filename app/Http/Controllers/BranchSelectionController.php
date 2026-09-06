<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BranchSelectionController extends Controller
{
    public function index()
    {
        return Inertia::render('Branch/Select', [
            'branches' => Branch::activeList(),
            'selectedBranchId' => session('branch_id'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
        ]);

        $branch = Branch::whereKey($validated['branch_id'])
            ->where('is_active', true)
            ->firstOrFail();

        $request->session()->put('branch_id', $branch->id);

        return back()->with('success', "Now shopping from {$branch->name}.");
    }
}
