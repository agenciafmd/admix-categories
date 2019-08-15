<?php

namespace Agenciafmd\Categories\Providers;

use Agenciafmd\Categories\Category;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    protected $namespace = 'Agenciafmd\Categories\Http\Controllers';

    public function boot()
    {
        parent::boot();

        Route::bind('tag', function($id) {
            return Category::where('type', request()->segment(3))->findOrFail($id);
        });
    }

    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();
    }

    protected function mapApiRoutes()
    {
        Route::middleware('api')
             ->namespace($this->namespace)
             ->group(__DIR__.'/../routes/api.php');
    }

    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(__DIR__.'/../routes/web.php');
    }
}
