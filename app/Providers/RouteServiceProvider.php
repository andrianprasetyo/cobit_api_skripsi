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
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {

            $list_route_api = [];
            $filesInFolder = \File::files(base_path('routes/Api'));
            foreach ($filesInFolder as $path) {
                $file = pathinfo($path);
                $file_route = $file['filename'] . '.php';
                $list_route_api[] = base_path('routes/Api/' . $file_route);
            }
            
            // dd($list_route_api);

            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('api')
                ->prefix('api')
                ->namespace('App\Http\Controllers\Api')
                ->group($list_route_api);

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
