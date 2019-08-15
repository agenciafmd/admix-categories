<?php

/*
|--------------------------------------------------------------------------
| ADMIX Routes
|--------------------------------------------------------------------------
*/

/* DEMO
Route::prefix(config('admix.url') . '/{{ pacotes }}/{{ tipos }}')
    ->name('admix.{{ pacotes }}.{{ tipos }}.')
    ->middleware(['auth:admix-web'])
    ->group(function () {
        Route::get('', '\Agenciafmd\Categories\Http\Controllers\CategoryController@index')
            ->name('index')
            ->middleware('can:view,\Agenciafmd\{{ Pacotes }}\{{ Tipo }}');
        Route::get('trash', '\Agenciafmd\Categories\Http\Controllers\CategoryController@index')
            ->name('trash')
            ->middleware('can:restore,\Agenciafmd\{{ Pacotes }}\{{ Tipo }}');
        Route::get('create', '\Agenciafmd\Categories\Http\Controllers\CategoryController@create')
            ->name('create')
            ->middleware('can:create,\Agenciafmd\{{ Pacotes }}\{{ Tipo }}');
        Route::post('', '\Agenciafmd\Categories\Http\Controllers\CategoryController@store')
            ->name('store')
            ->middleware('can:create,\Agenciafmd\{{ Pacotes }}\{{ Tipo }}');
        Route::get('{tag}', '\Agenciafmd\Categories\Http\Controllers\CategoryController@show')
            ->name('show')
            ->middleware('can:view,\Agenciafmd\{{ Pacotes }}\{{ Tipo }}');
        Route::get('{tag}/edit', '\Agenciafmd\Categories\Http\Controllers\CategoryController@edit')
            ->name('edit')
            ->middleware('can:update,\Agenciafmd\{{ Pacotes }}\{{ Tipo }}');
        Route::put('{tag}', '\Agenciafmd\Categories\Http\Controllers\CategoryController@update')
            ->name('update')
            ->middleware('can:update,\Agenciafmd\{{ Pacotes }}\{{ Tipo }}');
        Route::delete('destroy/{tag}', '\Agenciafmd\Categories\Http\Controllers\CategoryController@destroy')
            ->name('destroy')
            ->middleware('can:delete,\Agenciafmd\{{ Pacotes }}\{{ Tipo }}');
        Route::post('{id}/restore', '\Agenciafmd\Categories\Http\Controllers\CategoryController@restore')
            ->name('restore')
            ->middleware('can:restore,\Agenciafmd\{{ Pacotes }}\{{ Tipo }}');
        Route::post('batchDestroy', '\Agenciafmd\Categories\Http\Controllers\CategoryController@batchDestroy')
            ->name('batchDestroy')
            ->middleware('can:delete,\Agenciafmd\{{ Pacotes }}\{{ Tipo }}');
        Route::post('batchRestore', '\Agenciafmd\Categories\Http\Controllers\CategoryController@batchRestore')
            ->name('batchRestore')
            ->middleware('can:restore,\Agenciafmd\{{ Pacotes }}\{{ Tipo }}');
    });
*/
