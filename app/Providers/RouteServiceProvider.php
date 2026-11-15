<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Named limiters each keep their own counter. (A plain "throttle:N,1"
        // keys only by user, so every throttled route would share one budget.)
        $perUser = fn (string $name, int $perMinute) => RateLimiter::for($name, function (Request $request) use ($name, $perMinute) {
            return Limit::perMinute($perMinute)->by($name . ':' . ($request->user()?->id ?: $request->ip()));
        });

        $perUser('verify-code', 10);
        $perUser('resend-code', 3);
        $perUser('transfers', 30);
        $perUser('code-checks', 60);
        $perUser('tickets', 5);

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
