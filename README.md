## F&MD - Categories

[![Downloads](https://img.shields.io/packagist/dt/agenciafmd/admix-categories.svg?style=flat-square)](https://packagist.org/packages/agenciafmd/admix-categories)
[![Licença](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

- Categorias em qualquer lugar

## Instalação

```
composer require agenciafmd/admix-categories:dev-master
```

## Incorporando ao seu pacote

O que vamos fazer, é acoplar as categorias, no nosso pacote

Vamos assumir que nossa **tipos** irá se chamar **categorias** e nosso **pacote** se chamará **produtos**

Basicamente o que for `{{ tipos }}` vira `categories` e o que for `{{ pacotes }}` vira `products`

É muito importante manter o **case** e a **plurarização** das string

## Criação das tabelas

Crie o arquivo `packages/agenciafmd/{{ pacote }}/src/database/migrations/0000_00_00_000000_add_{{ tipo }}_id_field_on_{{ pacotes }}_table`

```php
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Add{Tipo}IdFieldOn{Pacotes}Table extends Migration
{
    public function up()
    {
        Schema::table('{pacotes}', function (Blueprint $table) {
            $table->integer('{tipo}_id')->default(0);
        });
    }

    public function down()
    {
        Schema::table('{pacotes}', function (Blueprint $table) {
            $table->dropColumn('{tipo}_id');
        });
    }
}
```

Execute as migrações

```
php artisan migrate
```


### Configurações

Crie o arquivo `packages/agenciafmd/{{ pacote }}/src/config/admix-categories.php`

```php
<?php

return [
    '{{ tipos }}' => [
        'name' => '{{ Tipos }}',
        'icon' => 'icon fe-minus',
        'star' => false,
        'description' => false,
        'sort' => 20,
        'default_sort' => [
            '-is_active',
            'sort',
            'name',
        ]
    ]
];
```

Carregue o arquivo acima em `/packages/agenciafmd/{{ pacote }}/src/Providers/{{ Pacote }}ServiceProvider.php`

```php
protected function loadConfigs()
{
    ...
    $this->mergeConfigFrom(__DIR__ . '/../config/admix-categories.php', 'admix-categories');
    ...
}
```

### Permissões

No arquivo `packages/agenciafmd/{{ pacote }}/src/config/gate.php` adicione antes das configurações do pacote

```
[
    'name' => '{{ Pacote }} » {{ Tipos }}',
    'policy' => '\Agenciafmd\{{ Pacotes }}\Policies\{{ Tipo }}Policy',
    'abilities' => [
        [
            'name' => 'visualizar',
            'method' => 'view',
        ],
        [
            'name' => 'criar',
            'method' => 'create',
        ],
        [
            'name' => 'atualizar',
            'method' => 'update',
        ],
        [
            'name' => 'deletar',
            'method' => 'delete',
        ],
        [
            'name' => 'restarurar',
            'method' => 'restore',
        ],
    ],
    'sort' => 10
],
```

### Validação

No arquivo `packages/agenciafmd/{{ pacote }}/src/Http/Requests/{{ Pacote }}Request.php` adicione

```
public function rules()
{
    return [
        ...
        '{{ tipo }}_id' => 'required|integer',
        ...
    ];
}

public function attributes()
{
    return [
        ...
        '{{ tipo }}_id' => {{ tipo }}',
        ...
    ];
}
```

### Politicas

Crie o arquivo `packages/agenciafmd/{{ pacote }}/src/Policies/{{ Tipo }}Policy.php`

```php
<?php

namespace Agenciafmd\{{ Pacotes }}\Policies;

use Agenciafmd\Admix\Policies\AdmixPolicy;

class {{ Tipo }}Policy extends AdmixPolicy
{
    //
}
```

### Registrando as politicas

No arquivo `packages/agenciafmd/{{ pacote }}/src/Providers/AuthServiceProviders.php` adicione

```php
...
protected $policies = [
    ...
    '\Agenciafmd\{{ Pacotes }}\{{ Tipo }}' => '\Agenciafmd\{{ Pacotes }}\Policies\{{ Tipo }}Policy',
];
...
```

### Menu

No arquivo `packages/agenciafmd/{{ pacote }}/src/resources/views/partials/menus/item.blade.php` faça as correções

```blade
@if (!((admix_cannot('view', '\Agenciafmd\{{ Pacotes }}\{{ Pacote }}')) && (admix_cannot('view', '\Agenciafmd\{{ Pacotes }}\{{ Tipo }}'))))
    <li class="nav-item">
        <a class="nav-link @if (admix_is_active(route('admix.{{ pacotes }}.index')) || admix_is_active(route('admix.{{ tipos }}.index'))) active @endif"
           href="#sidebar-settings" data-toggle="collapse" data-parent="#menu" role="button"
           aria-expanded="{{ (admix_is_active(route('admix.{{ pacotes }}.index')) || admix_is_active(route('admix.{{ tipos }}.index'))) ? 'true' : 'false' }}">
            <span class="nav-icon">
                <i class="icon {{ config('local-{{ pacotes  }}.icon') }}"></i>
            </span>
            <span class="nav-text">
                {{ config('local-{{ pacotes  }}.name') }}
            </span>
        </a>
        <div
            class="navbar-subnav collapse @if (admix_is_active(route('admix.{{ pacotes }}.index')) || admix_is_active(route('admix.{{ tipos }}.index')) ) show @endif"
            id="sidebar-settings">
            <ul class="nav">
                @can('view', '\Agenciafmd\{{ Pacotes }}\{{ Tipo }}')
                    <li class="nav-item">
                        <a class="nav-link {{ admix_is_active(route('admix.{{ pacotes }}.{{ tipos }}.index')) ? 'active' : '' }}"
                           href="{{ route('admix.{{ pacotes }}.{{ tipos }}.index') }}">
                            <span class="nav-icon">
                                <i class="icon fe-minus"></i>
                            </span>
                            <span class="nav-text">
                                {{ config('admix-categories.{{ tipos  }}.name') }}
                            </span>
                        </a>
                    </li>
                @endcan
                @can('view', '\Agenciafmd\{{ Pacotes }}\{{ Pacote }}')
                    <li class="nav-item">
                        <a class="nav-link {{ admix_is_active(route('admix.{{ pacotes }}.index')) ? 'active' : '' }}"
                           href="{{ route('admix.{{ pacotes }}.index') }}">
                            <span class="nav-icon">
                                <i class="icon fe-minus"></i>
                            </span>
                            <span class="nav-text">
                                {{ config('local-{{ pacotes  }}.name') }}
                            </span>
                        </a>
                    </li>
                @endcan
            </ul>
        </div>
    </li>
@endif
```

### Listagem

No arquivo `packages/agenciafmd/{{ pacote }}/src/resources/views/index.blade.php` adicione na `@section('filters')`

```blade
@include('agenciafmd/categories::partials.form.filter', [
    'type' => 'lines'
])
@endsection
```

### Formulário

No arquivo `packages/agenciafmd/{{ pacote }}/src/resources/views/form.blade.php` adicione

```blade
@include('agenciafmd/categories::partials.form.select', [
    'type' => 'lines',
    'required' => true
])
```

### Rotas

No arquivo `packages/agenciafmd/{{ pacote }}/src/routes/web.php` e adicione **antes** das rotas do pacote

```
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
```

### Model
Crie o arquivo `packages/agenciafmd/{{ pacote }}/src/{{ Tipo }}.php` e adicione

```php
<?php

namespace Agenciafmd\{{ Pacotes }};

use Agenciafmd\Categories\CategoryBase;
use Illuminate\Database\Eloquent\Builder;

class {{ Tipo }} extends CategoryBase
{
    protected $table = 'categories';

    protected $attributes = [
        'type' => '{{ tipos }}',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('type', function (Builder $builder) {
            $builder->where('type', '{{ tipos }}');
        });
    }
}
```

### Relacionamento
No arquivo `packages/agenciafmd/{{ pacote }}/src/{{ Pacote }}.php` adicione

```php
use Agenciafmd\{{ Pacotes }}\{{ Tipo }};

...

public function {{ tipo }}()
{
    return $this->belongsTo({{ Tipo }}::class);
}
```

### Factory
Crie o arquivo `packages/agenciafmd/{{ pacote }}/src/database/factories/{{ Pacotes}}{{ Tipos }}Factory.php` e adicione

```php
<?php

use Agenciafmd\{{ Pacotes }}\{{ Tipo }};

$factory->define({{ Tipo }}::class, function (\Faker\Generator $faker) {
    return [
        'is_active' => $faker->optional(0.3, 1)
            ->randomElement([0]),
        'name' => ucfirst($faker->sentence())
    ];
});
```

### Seed com Factory
Crie o arquivo `packages/agenciafmd/{{ pacote }}/src/database/seeds/{{ Pacotes}}{{ Tipos }}TableSeeder.php` e adicione

```php
<?php

use Agenciafmd\{{ Pacotes }}\{{ Tipo }};
use Illuminate\Database\Seeder;

class {{ Pacotes }}{{ Tipos }}TableSeeder extends Seeder
{
    protected $total = 100;

    public function run()
    {
        {{ Tipo }}::withTrashed()->get()->each->forceDelete();

        $faker = Faker\Factory::create();

        $this->command->getOutput()
            ->progressStart($this->total);
        factory({{ Tipo }}::class, $this->total)
            ->create()
            ->each(function () {
                $this->command->getOutput()
                    ->progressAdvance();
            });
        $this->command->getOutput()
            ->progressFinish();
    }
}
```

### Seed simples sem Factory
Crie o arquivo `packages/agenciafmd/{{ pacote }}/src/database/seeds/{{ Pacotes}}{{ Tipos }}TableSeeder.php` e adicione

```php
<?php

use Agenciafmd\{{ Pacotes }}\{{ Tipo }};
use Illuminate\Database\Seeder;

class {{ Pacotes }}{{ Tipos }}TableSeeder extends Seeder
{
    public function run()
    {
        {{ Tipo }}::withTrashed()->get()->each->forceDelete();

        $items = collect([
            'Acessórios',
            'Temperos Prontos',
        ]);

        $this->command->getOutput()
            ->progressStart($items->count());
        $items->each(function ($item) {
            Category::create([
                'is_active' => '1',
                'name' => $item,
            ]);
            $this->command->getOutput()
                ->progressAdvance();
        });
        $this->command->getOutput()
            ->progressFinish();
    }
}
```