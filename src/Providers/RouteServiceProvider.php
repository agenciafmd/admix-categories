<?php

namespace Agenciafmd\Categories\Providers;

use Agenciafmd\Categories\Models\Category;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::bind('tag', function ($id) {
            return Category::where('type', request()->segment(3))
                ->findOrFail($id);
        });

        $this->routes(function () {
            Route::prefix(config('admix.url'))
                ->middleware(['web', 'auth:admix-web'])
                ->group(__DIR__ . '/../routes/web.php');

            Route::prefix(config('admix.url') . '/api')
                ->middleware('api')
                ->group(__DIR__ . '/../routes/api.php');
        });
    }
}
