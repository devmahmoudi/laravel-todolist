<?php

namespace App\Http\Middleware;

use App\Models\Group;
use App\Models\Todo;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

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
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $request->user(),
            ],
            'ziggy' => fn(): array => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'groups' => fn() => Group::all(['id', 'name']),
            'toasts' => function () use ($request) {
                return [
                    'success' => $request->session()->get('toast.success'),
                    'error' => $request->session()->get('toast.error'),
                ];
            },
            'activeGroup' => $this->getActiveGroup(),
        ];
    }

    /**
     * Returns active Group model if current route parameters
     * has a parameter instance of Group or Todo model, otherwise returns null
     *
     * @return Group|null
     */
    public function getActiveGroup(): ?Group
    {
        foreach (Route::getCurrentRoute()->parameters() as $param) {
            if ($param instanceof Todo)
                return $param->group;
            else if ($param instanceof Group)
                return $param;
        }

        return null;
    }
}
