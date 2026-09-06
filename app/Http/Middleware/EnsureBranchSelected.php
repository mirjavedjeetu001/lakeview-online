<?php

namespace App\Http\Middleware;

use App\Models\Branch;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBranchSelected
{
    public function handle(Request $request, Closure $next): Response
    {
        $branchId = $request->session()->get('branch_id');

        if ($branchId && Branch::activeList()->contains('id', (int) $branchId)) {
            return $next($request);
        }

        $request->session()->forget('branch_id');

        return redirect()->route('home', ['choose_branch' => 1]);
    }
}
