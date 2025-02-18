<?php

namespace Agenciafmd\Categories\Providers;

use Agenciafmd\Categories\View\Components;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadBladeComponents();

        $this->loadBladeDirectives();

        $this->loadBladeComposers();

        $this->setMenu();

        $this->loadViews();

        $this->publish();
    }

    public function register(): void
    {
        //
    }

    private function loadBladeComponents(): void
    {
        Blade::componentNamespace('Agenciafmd\\Categories\\View\\Components', 'admix-categories');
        Blade::component('categories::form.select', Components\Forms\Inputs\Select::class);
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
        //        $this->app->make('admix-menu')
        //            ->push((object)[
        //                'component' => 'admix-categories::aside.category',
        //                'ord' => config('admix-categories.sort'),
        //            ]);
    }

    private function loadViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'admix-categories');
    }

    private function publish(): void
    {
        // $this->publishes([
        //     __DIR__ . '/../resources/views' => base_path('resources/views/vendor/agenciafmd/categories'),
        // ], 'admix-categories:views');
    }
}
