<?php

namespace Agenciafmd\Categories\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Agenciafmd\Categories\Models\Category;

class CommandServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            //
        ]);

        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $minutes = config('admix.schedule.minutes');

            $schedule->command('model:prune', [
                '--model' => [
                    Category::class,
                ],
            ])
                ->dailyAt("03:{$minutes}");
        });
    }
}
