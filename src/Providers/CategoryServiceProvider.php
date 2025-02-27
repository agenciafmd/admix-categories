<?php

namespace Agenciafmd\Categories\Providers;

use Illuminate\Support\ServiceProvider;

class CategoryServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->bootProviders();

        $this->bootMigrations();

        $this->bootTranslations();

        $this->bootPublish();
    }

    public function register(): void
    {
        $this->registerConfigs();
    }

    private function bootProviders(): void
    {
        $this->app->register(BladeServiceProvider::class);
        $this->app->register(CommandServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(AuthServiceProvider::class);
        $this->app->register(LivewireServiceProvider::class);
    }

    private function bootPublish(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/admix-categories.php' => base_path('config/admix-categories.php'),
        ], 'admix-categories:config');

        $this->publishes([
            __DIR__ . '/../../database/seeders/CategoryTableSeeder.php' => base_path('database/seeders/CategoryTableSeeder.php'),
        ], 'admix-categories:seeders');

        $this->publishes([
            __DIR__ . '/../../lang/pt_BR' => lang_path('pt_BR'),
        ], ['admix-categories:translations', 'admix-translations']);
    }

    private function bootMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }

    private function bootTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../../lang', 'admix-categories');
        $this->loadJsonTranslationsFrom(__DIR__ . '/../../lang');
    }

    private function registerConfigs(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/admix-categories.php', 'admix-categories');
        $this->mergeConfigFrom(__DIR__ . '/../../config/audit-alias.php', 'audit-alias');
    }
}
