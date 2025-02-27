<?php

namespace Agenciafmd\Categories\Providers;

use Agenciafmd\Categories\View\Components;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->bootBladeComponents();

        $this->bootBladeDirectives();

        $this->bootBladeComposers();

        $this->bootMenu();

        $this->bootViews();

        $this->bootPublish();
    }

    public function register(): void
    {
        //
    }

    private function bootBladeComponents(): void
    {
        Blade::componentNamespace('Agenciafmd\\Categories\\View\\Components', 'admix-categories');
        Blade::component('categories::form.select', Components\Forms\Inputs\Select::class);
    }

    private function bootBladeComposers(): void
    {
        //
    }

    private function bootBladeDirectives(): void
    {
        //
    }

    private function bootMenu(): void
    {
        //        $this->app->make('admix-menu')
        //            ->push((object)[
        //                'component' => 'admix-categories::aside.category',
        //                'ord' => config('admix-categories.sort'),
        //            ]);
    }

    private function bootViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'admix-categories');
    }

    private function bootPublish(): void
    {
        // $this->publishes([
        //     __DIR__ . '/../resources/views' => base_path('resources/views/vendor/agenciafmd/categories'),
        // ], 'admix-categories:views');
    }
}
