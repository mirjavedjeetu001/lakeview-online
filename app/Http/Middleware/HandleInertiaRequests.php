<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $branches = \App\Models\Branch::activeList();
        $selectedBranch = null;
        if ($request->session()->get('branch_id')) {
            $selectedBranch = $branches->firstWhere('id', (int) $request->session()->get('branch_id'));
        }
        $mainBranch = $branches->first(
            fn ($branch) => str_contains(strtolower((string) $branch->name), 'main')
        ) ?: $branches->first();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'role' => $request->user()->role,
                    'phone' => $request->user()->phone,
                ] : null,
            ],
            'settings' => \App\Models\Setting::getAllByGroup(),
            'branches' => fn () => $branches,
            'selectedBranch' => $selectedBranch,
            'mainBranch' => $mainBranch,
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }
}
