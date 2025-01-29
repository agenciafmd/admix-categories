<?php

use Agenciafmd\Categories\Livewire\Pages;
use Illuminate\Support\Facades\Route;

Route::get('/categories', Pages\Category\Index::class)
    ->name('admix.categories.index');
Route::get('/categories/trash', Pages\Category\Index::class)
    ->name('admix.categories.trash');
Route::get('/categories/create', Pages\Category\Component::class)
    ->name('admix.categories.create');
Route::get('/categories/{category}/edit', Pages\Category\Component::class)
    ->name('admix.categories.edit');
