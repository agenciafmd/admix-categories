<?php

use Agenciafmd\Categories\Livewire\Pages;
use Illuminate\Support\Facades\Route;

Route::get('/{categoryModel}/{categoryType}', Pages\Category\Index::class)
    ->name('admix.categories.index');
Route::get('/{categoryModel}/{categoryType}/trash', Pages\Category\Index::class)
    ->name('admix.categories.trash');
Route::get('/{categoryModel}/{categoryType}/create', Pages\Category\Component::class)
    ->name('admix.categories.create');
Route::get('/{categoryModel}/{categoryType}/{category}/edit', Pages\Category\Component::class)
    ->name('admix.categories.edit');
