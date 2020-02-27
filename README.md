## F&MD - Categories

[![Downloads](https://img.shields.io/packagist/dt/agenciafmd/admix-categories.svg?style=flat-square)](https://packagist.org/packages/agenciafmd/admix-categories)
[![Licença](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

- Categorias em qualquer lugar

### Instalação

```
composer require agenciafmd/admix-categories:dev-master
```

### Incorporando ao seu pacote

Vamos usar o pacote **[admix-articles](https://github.com/agenciafmd/admix-articles)** como exemplo 

### Migrações

Podemos criar a migração caso o pacote já esteja em uso ou adicionamos a linha no nosso pacote novinho

Ex. `packages/agenciafmd/admix-articles/src/database/migrations/0000_00_00_000000_add_category_id_field_on_articles_table`
```php
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCategoryIdFieldOnArticlesTable extends Migration
{
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->integer('category_id')->default(0);
        });
    }

    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('category_id');
        });
    }
}
```

### Configurações

Ao criarmos o arquivo de configuração, precisamos ter em mente, que o indice será composto de `model pai no plural - model filha no plural`

No caminho `packages/agenciafmd/admix-articles/src/config/`, crie os arquivos abaixo

**admix-categories.php**
```php
<?php

return [
    'articles-categories' => [
        'name' => 'Categorias',
        'icon' => 'icon fe-minus',
        'star' => false,
        'description' => false,
        'sort' => 20,
        'default_sort' => [
            '-is_active',
            'sort',
            'name',
        ],
        // caso seja necessário, utilize o campo abaixo para o seed
        //'items' => [ 
        //    'Coletores',
        //    'Pré-Coletores',
        //    'Transferências de Calor e Massa',
        //],
    ]
];
```

**upload-configs.php**
```
<?php

return [
    ...
    'articles-categories' => [
        'image' => [
            'name' => 'Imagem',
            'faker_dir' => false, #database_path('faker/categories/image'),
            'multiple' => false,
            'width' => 450,
            'height' => 500,
            'quality' => 95,
            'optimize' => true,
            'crop' => true,
        ],
        ...
    ],
];
```

Carregue os arquivos em `/packages/agenciafmd/admix-articles/src/Providers/ArticlesServiceProvider.php`

```php
protected function loadConfigs()
{
    ...
    $this->mergeConfigFrom(__DIR__ . '/../config/admix-categories.php', 'admix-categories');
    $this->mergeConfigFrom(__DIR__ . '/../config/upload-configs.php', 'upload-configs');
}
```

### Permissões

No arquivo `packages/agenciafmd/admix-articles/src/config/gate.php` adicione antes das configurações do pacote

```php
[
    'name' => config('admix-articles.name') . ' » Categorias',
    'policy' => '\Agenciafmd\Articles\Policies\CategoryPolicy',
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

No arquivo `packages/agenciafmd/admix-articles/src/Http/Requests/CategoryRequest.php` adicione

```
public function rules()
{
    return [
        ...
        'category_id' => 'required|integer',
        ...
    ];
}

public function attributes()
{
    return [
        ...
        'category_id' => 'categoria',
        ...
    ];
}
```

### Politicas

Crie o arquivo `packages/agenciafmd/admix-articles/src/Policies/CategoryPolicy.php`

```php
<?php

namespace Agenciafmd\Articles\Policies;

use Agenciafmd\Admix\Policies\AdmixPolicy;

class CateogryPolicy extends AdmixPolicy
{
    //
}
```

### Registrando as politicas

No arquivo `packages/agenciafmd/admix-articles/src/Providers/AuthServiceProviders.php` adicione

```php
...
protected $policies = [
    ...
    '\Agenciafmd\Articles\Category' => '\Agenciafmd\Articles\Policies\CategoryPolicy',
];
...
```

### Menu

No arquivo `packages/agenciafmd/admix-articles/src/resources/views/partials/menus/item.blade.php` faça as correções

```blade
@if (!((admix_cannot('view', '\Agenciafmd\Articles\Article')) && (admix_cannot('view', '\Agenciafmd\Articles\Category'))))
    <li class="nav-item">
        <a class="nav-link @if (admix_is_active(route('admix.articles.index')) || admix_is_active(route('admix.articles.categories.index'))) active @endif"
           href="#sidebar-settings" data-toggle="collapse" data-parent="#menu" role="button"
           aria-expanded="{{ (admix_is_active(route('admix.articles.index')) || admix_is_active(route('admix.articles.categories.index'))) ? 'true' : 'false' }}">
            <span class="nav-icon">
                <i class="icon {{ config('admix-articles.icon') }}"></i>
            </span>
            <span class="nav-text">
                {{ config('admix-articles.name') }}
            </span>
        </a>
        <div
            class="navbar-subnav collapse @if (admix_is_active(route('admix.articles.index')) || admix_is_active(route('admix.articles.categories.index')) ) show @endif"
            id="sidebar-settings">
            <ul class="nav">
                @can('view', '\Agenciafmd\Articles\Category')
                    <li class="nav-item">
                        <a class="nav-link {{ admix_is_active(route('admix.articles.categories.index')) ? 'active' : '' }}"
                           href="{{ route('admix.articles.categories.index') }}">
                            <span class="nav-icon">
                                <i class="icon fe-minus"></i>
                            </span>
                            <span class="nav-text">
                                {{ config('admix-categories.articles-categories.name') }}
                            </span>
                        </a>
                    </li>
                @endcan
                @can('view', '\Agenciafmd\Articles\Article')
                    <li class="nav-item">
                        <a class="nav-link {{ admix_is_active(route('admix.articles.index')) ? 'active' : '' }}"
                           href="{{ route('admix.articles.index') }}">
                            <span class="nav-icon">
                                <i class="icon fe-minus"></i>
                            </span>
                            <span class="nav-text">
                                {{ config('admix-articles.name') }}
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

No arquivo `packages/agenciafmd/admix-articles/src/resources/views/index.blade.php` adicione na `@section('filters')`

```blade
@include('agenciafmd/categories::partials.form.filter', [
    'label' => config('admix-categories.articles-categories.name'),
    'type' => 'articles-categories',
    'name' => 'category_id'
])
```

### Formulário

No arquivo `packages/agenciafmd/admix-articles/src/resources/views/form.blade.php` adicione

```blade
@include('agenciafmd/categories::partials.form.select', [
    'label' => config('admix-categories.articles-categories.name'),
    'type' => 'articles-categories',
    'name' => 'category_id',
    'required' => true
])
```

### Rotas

No arquivo `packages/agenciafmd/admix-articles/src/routes/web.php` e adicione **antes** das rotas do pacote

```
Route::prefix(config('admix.url') . '/articles/categories')
    ->name('admix.articles.categories.')
    ->middleware(['auth:admix-web'])
    ->group(function () {
        Route::get('', '\Agenciafmd\Categories\Http\Controllers\CategoryController@index')
            ->name('index')
            ->middleware('can:view,\Agenciafmd\Articles\Category');
        Route::get('trash', '\Agenciafmd\Categories\Http\Controllers\CategoryController@index')
            ->name('trash')
            ->middleware('can:restore,\Agenciafmd\Articles\Category');
        Route::get('create', '\Agenciafmd\Categories\Http\Controllers\CategoryController@create')
            ->name('create')
            ->middleware('can:create,\Agenciafmd\Articles\Category');
        Route::post('', '\Agenciafmd\Categories\Http\Controllers\CategoryController@store')
            ->name('store')
            ->middleware('can:create,\Agenciafmd\Articles\Category');
        Route::get('{category}', '\Agenciafmd\Categories\Http\Controllers\CategoryController@show')
            ->name('show')
            ->middleware('can:view,\Agenciafmd\Articles\Category');
        Route::get('{category}/edit', '\Agenciafmd\Categories\Http\Controllers\CategoryController@edit')
            ->name('edit')
            ->middleware('can:update,\Agenciafmd\Articles\Category');
        Route::put('{category}', '\Agenciafmd\Categories\Http\Controllers\CategoryController@update')
            ->name('update')
            ->middleware('can:update,\Agenciafmd\Articles\Category');
        Route::delete('destroy/{category}', '\Agenciafmd\Categories\Http\Controllers\CategoryController@destroy')
            ->name('destroy')
            ->middleware('can:delete,\Agenciafmd\Articles\Category');
        Route::post('{id}/restore', '\Agenciafmd\Categories\Http\Controllers\CategoryController@restore')
            ->name('restore')
            ->middleware('can:restore,\Agenciafmd\Articles\Category');
        Route::post('batchDestroy', '\Agenciafmd\Categories\Http\Controllers\CategoryController@batchDestroy')
            ->name('batchDestroy')
            ->middleware('can:delete,\Agenciafmd\Articles\Category');
        Route::post('batchRestore', '\Agenciafmd\Categories\Http\Controllers\CategoryController@batchRestore')
            ->name('batchRestore')
            ->middleware('can:restore,\Agenciafmd\Articles\Category');
    });
```

### Model
Crie o arquivo `packages/agenciafmd/admix-articles/src/Category.php` e adicione

```php
<?php

namespace Agenciafmd\Articles;

use Agenciafmd\Categories\Category as CategoryBase;
use Illuminate\Database\Eloquent\Builder;

class Category extends CategoryBase
{
    protected $table = 'categories';

    protected $attributes = [
        'type' => 'articles-categories',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('type', function (Builder $builder) {
            $builder->where('type', 'articles-categories');
        });
    }

    public function getMorphClass()
    {
        return CategoryBase::class;
    }

    public function scopeSort($query, $type = 'articles-categories')
    {
        $sorts = default_sort(config("admix-categories.{$type}.default_sort"));

        foreach ($sorts as $sort) {
            $query->orderBy($sort['field'], $sort['direction']);
        }
    }
}
```

### Relacionamento
No arquivo `packages/agenciafmd/admix-articles/src/Article.php` adicione

```php
use Agenciafmd\Articles\Category;

...

public function category()
{
    return $this->belongsTo(Category::class);
}
```

### Factory
Crie o arquivo `packages/agenciafmd/admix-articles/src/database/factories/ArticlesCategoriesFactory.php` e adicione

```php
<?php

use Agenciafmd\Articles\Category;

$factory->define(Category::class, function (\Faker\Generator $faker) {
    return [
        'is_active' => $faker->optional(0.3, 1)
            ->randomElement([0]),
        'name' => ucfirst($faker->sentence())
    ];
});
```

### Seed com Factory
Crie o arquivo `packages/agenciafmd/admix-articles/src/database/seeds/ArticlesCategoriesTableSeeder.php` e adicione

```php
<?php

use Agenciafmd\Articles\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArticlesCategoriesTableSeeder extends Seeder
{
    protected $total = 30;

    public function run()
    {
        Category::withTrashed()
            ->get()->each->forceDelete();

        DB::table('media')
            ->where('model_type', 'Agenciafmd\\Articles\\Category')
            ->delete();

        $faker = Faker\Factory::create('pt_BR');

        $this->command->getOutput()
            ->progressStart($this->total);

        factory(Category::class, $this->total)
            ->create()
            ->each(function ($category) use ($faker) {
                foreach (config('upload-configs.articles-categories') as $key => $image) {
                    $fakerDir = __DIR__ . '/../faker/categories/' . $key;
    
                    if ($image['faker_dir']) {
                        $fakerDir = $image['faker_dir'];
                    }
    
                    if ($image['multiple']) {
                        $items = $faker->numberBetween(0, 6);
                        for ($i = 0; $i < $items; $i++) {
                            $category->doUploadMultiple($faker->file($fakerDir, storage_path('admix/tmp')), $key);
                        }
                    } else {
                        $category->doUpload($faker->file($fakerDir, storage_path('admix/tmp')), $key);
                    }
                }

                $category->save();
    
                $this->command->getOutput()
                    ->progressAdvance();
            });

        $this->command->getOutput()
            ->progressFinish();
    }
}
```

### Seed sem Factory
Crie o arquivo `packages/agenciafmd/admix-articles/src/database/seeds/ArticlesCategoriesTableSeeder.php` e adicione

```php
<?php

use Agenciafmd\Articles\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArticlesCategoriesTableSeeder extends Seeder
{
    public function run()
    {
        Category::withTrashed()
            ->get()->each->forceDelete();

        DB::table('media')
            ->where('model_type', 'Agenciafmd\\Articles\\Category')
            ->delete();

        $items = collect(config('admix-categories.articles-categories.items'));

        $faker = Faker\Factory::create('pt_BR');

        $this->command->getOutput()
            ->progressStart($items->count());
        $items->each(function ($item) use ($faker) {
            $category = Category::create([
                'is_active' => '1',
                'name' => $item,
            ]);

            // para imagens atreladas no seed
            //foreach (config('upload-configs.products-categories') as $key => $image) {
            //    $fakerPath = __DIR__ . '/../faker/categories/' . Str::slug($item) . '-' . $key . '.jpg';
            //    copy($fakerPath, storage_path('admix/tmp/' . basename($fakerPath)));
            //    $category->doUpload(storage_path('admix/tmp/' . basename($fakerPath)), $key);
            //}

            foreach (config('upload-configs.articles-categories') as $key => $image) {
                $fakerDir = __DIR__ . '/../faker/categories/' . $key;

                if ($image['faker_dir']) {
                    $fakerDir = $image['faker_dir'];
                }

                if ($image['multiple']) {
                    $items = $faker->numberBetween(0, 6);
                    for ($i = 0; $i < $items; $i++) {
                        $category->doUploadMultiple($faker->file($fakerDir, storage_path('admix/tmp')), $key);
                    }
                } else {
                    $category->doUpload($faker->file($fakerDir, storage_path('admix/tmp')), $key);
                }
            }

            $category->save();

            $this->command->getOutput()
                ->progressAdvance();
        });

        $this->command->getOutput()
            ->progressFinish();
    }
}
```

### Seed na ArticlesTableSeeder

```
...
$categories = Category::pluck('id');
...
    ->each(function ($item) use ($faker, $categories) {
    
        $item->category_id = $faker->randomElement($categories);
...
    $item->save();
...
```