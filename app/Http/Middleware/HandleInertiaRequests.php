<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\Category;

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
        return [
            ...parent::share($request),

            // Authentication data - available in all pages via page.props.auth
            'auth' => [
                'user' => $request->user(),
            ],

            // Categories - available in navbar dropdown
            'categories' => function () {
                return Category::active()
                    ->ordered()
                    ->take(8) // Limit to 8 main categories in navbar
                    ->get(['id', 'name', 'slug', 'icon'])
                    ->toArray();
            },

            // Cart items - available in navbar via page.props.items
            'items' => function () use ($request) {
                if ($request->session()->has('cart')) {
                    return $request->session()->get('cart', []);
                }
                return [];
            },

            // Flash messages - for success/error notifications
            'flash' => [
                'message' => fn() => $request->session()->get('message'),
                'error' => fn() => $request->session()->get('error'),
            ],
        ];
    }
}
