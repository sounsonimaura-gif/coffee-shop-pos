<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Shared Inertia props (available as page.props on the client).
     */
    public function share(Request $request): array
    {
        $locale = App::getLocale();

        $user = $request->user();
        $authPayload = null;

        if ($user) {
            $user->loadMissing(['role.permissions', 'company', 'branches']);

            $authPayload = [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'company_id' => $user->company_id,
                    'role_id' => $user->role_id,
                    'default_branch_id' => $user->default_branch_id,
                    'role' => $user->role?->only(['id', 'name', 'is_system']),
                    'permissions' => $user->permissionNames(),
                    'branches' => $user->branches->map(fn ($b) => [
                        'id' => $b->id,
                        'name' => $b->name,
                        'code' => $b->branch_code,
                    ])->values(),
                ],
                'company' => $user->company?->only(['id', 'name', 'company_code', 'currency_code', 'language_code']),
                'active_branch_id' => session('active_branch_id'),
            ];
        }

        return [
            ...parent::share($request),

            'app' => [
                'name' => config('app.name', 'Coffee Shop POS'),
                'env' => app()->environment(),
            ],

            'auth' => $authPayload,

            'locale' => $locale,
            'available_locales' => ['en', 'kh'],
            'translations' => $this->loadTranslations($locale),

            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'warning' => fn () => $request->session()->get('warning'),
                'info' => fn () => $request->session()->get('info'),
            ],

            'errors' => function () use ($request) {
                return $request->session()->get('errors')
                    ? $request->session()->get('errors')->getBag('default')->getMessages()
                    : (object) [];
            },
        ];
    }

    private function loadTranslations(string $locale): array
    {
        $translations = [];
        foreach (['coffee', 'auth', 'validation'] as $namespace) {
            $loaded = Lang::get($namespace, [], $locale);
            if (is_array($loaded)) {
                $translations[$namespace] = $this->flatten($loaded);
            }
        }

        return $translations;
    }

    private function flatten(array $array, string $prefix = ''): array
    {
        $out = [];
        foreach ($array as $key => $value) {
            $compoundKey = $prefix === '' ? (string) $key : "{$prefix}.{$key}";
            if (is_array($value)) {
                $out = array_merge($out, $this->flatten($value, $compoundKey));
            } else {
                $out[$compoundKey] = $value;
            }
        }

        return $out;
    }
}
