<?php

namespace Agenciafmd\Categories\Providers;

use Agenciafmd\Categories\Livewire\Pages;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class LivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('agenciafmd.categories.livewire.pages.category.index', Pages\Category\Index::class);
        Livewire::component('agenciafmd.categories.livewire.pages.category.component', Pages\Category\Component::class);
    }

    public function register(): void
    {
        //
    }
}
