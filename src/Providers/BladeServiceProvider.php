<?php

namespace Agenciafmd\Categories\Providers;

use Agenciafmd\Categories\Http\Components\Search;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {

        $prefix = config('admix-ui.prefix');

        $this->loadBladeComponents();

        $this->loadBladeDirectives();

        $this->loadBladeComposers();

        $this->setMenu();

        $this->loadViews();

        $this->publish();

        Blade::component($prefix . 'form.input-search', Search::class);
    }

    public function register(): void
    {
        //
    }

    private function loadBladeComponents(): void
    {
        Blade::componentNamespace('Agenciafmd\\Categories\\Http\\Components', 'local-categories');
    }

    private function loadBladeComposers(): void
    {
        //
    }

    private function loadBladeDirectives(): void
    {
        //
    }

    private function setMenu(): void
    {
        $this->app->make('admix-menu')
            ->push((object)[
                'component' => 'local-categories::aside.category',
                'ord' => config('local-categories.sort'),
            ]);
    }

    private function loadViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'local-categories');
    }

    private function publish(): void
    {
        // $this->publishes([
        //     __DIR__ . '/../resources/views' => base_path('resources/views/vendor/agenciafmd/categories'),
        // ], 'local-categories:views');
    }
}
