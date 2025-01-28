## F&MD - Categories

![Área Administrativa](https://github.com/agenciafmd/admix-categories/raw/master/docs/screenshot.png "Área Administrativa")

[![Downloads](https://img.shields.io/packagist/dt/agenciafmd/admix-categories.svg?style=flat-square)](https://packagist.org/packages/agenciafmd/admix-categories)
[![Licença](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

- Categorias em qualquer lugar

### Instalação

```bash
composer require agenciafmd/admix-categories:v11.x-dev
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
        'image' => [ //nome do campo
            'label' => 'imagem', //label do campo
            'multiple' => false, //se permite o upload multiplo
            'faker_dir' => false, #database_path('faker/articles-categories/image'),
            'sources' => [
                [
                    'conversion' => 'min-width-1366',
                    'media' => '(min-width: 1366px)',
                    'width' => 1024, // 16:9
                    'height' => 576,
                ],
                [
                    'conversion' => 'min-width-1280',
                    'media' => '(min-width: 1280px)',
                    'width' => 776,
                    'height' => 437,
                ],
            ],
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
    'name' => config('admix-articles.name') . ' » ' . config('admix-categories.articles-categories.name'),
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
            'name' => 'restaurar',
            'method' => 'restore',
        ],
    ],
    'sort' => 10
],
```

### Validação

No arquivo `packages/agenciafmd/admix-articles/src/Http/Requests/ArticleRequest.php` adicione

```
public function rules()
{
    return [
        ...
        'category_id' => [
            'required',
            'integer',
        ],
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

class CategoryPolicy extends AdmixPolicy
{
    //
}
```

### Registrando as politicas

No arquivo `packages/agenciafmd/admix-articles/src/Providers/AuthServiceProviders.php` adicione

```php
use Agenciafmd\Articles\Policies\CategoryPolicy;
use Agenciafmd\Articles\Models\Category;

...
protected $policies = [
    ...
    Category::class => CategoryPolicy::class,
];
...
```

### Menu

No arquivo `packages/agenciafmd/admix-articles/src/resources/views/partials/menus/item.blade.php` faça as correções

```blade
@can('view', [
    \Agenciafmd\Articles\Models\Category::class,
    \Agenciafmd\Articles\Models\Article::class,
])
    <li class="nav-item">
        <a class="nav-link {{ (Str::startsWith(request()->route()->getName(), 'admix.articles')) ? 'active' : '' }}"
           href="#sidebar-articles" data-toggle="collapse" data-parent="#menu" role="button"
           aria-expanded="{{ (Str::startsWith(request()->route()->getName(), 'admix.articles')) ? 'true' : 'false' }}">
            <span class="nav-icon">
                <i class="icon {{ config('admix-articles.icon') }}"></i>
            </span>
            <span class="nav-text">
                {{ config('admix-articles.name') }}
            </span>
        </a>
        <div class="navbar-subnav collapse {{ (Str::startsWith(request()->route()->getName(), 'admix.articles')) ? 'show' : '' }}"
             id="sidebar-articles">
            <ul class="nav">
                @can('view', \Agenciafmd\Articles\Models\Category::class)
                    <li class="nav-item">
                        <a class="nav-link {{ (Str::startsWith(request()->route()->getName(), 'admix.articles.categories')) ? 'active' : '' }}"
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
                @can('view', \Agenciafmd\Articles\Models\Article::class)
                    <li class="nav-item">
                        <a class="nav-link {{ (Str::startsWith(request()->route()->getName(), 'admix.articles') && !Str::startsWith(request()->route()->getName(), 'admix.articles.categories')) ? 'active' : '' }}"
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

### Request

Crie o arquivo `packages/agenciafmd/admix-articles/src/Http/Requests/CategoryRequest.php`

```php
<?php

namespace Agenciafmd\Articles\Http\Requests;

use Agenciafmd\Categories\Http\Requests\CategoryRequest as BaseCategoryRequest;

class CategoryRequest extends BaseCategoryRequest
{
    //
}
```

### Controller

Crie o arquivo `packages/agenciafmd/admix-articles/src/Http/Controllers/CategoryController.php`

```php
<?php

namespace Agenciafmd\Articles\Http\Controllers;

use Agenciafmd\Articles\Models\Category;
use Agenciafmd\Articles\Http\Requests\CategoryRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\QueryBuilder;

class CategoryController extends Controller
{
    protected $categoryModel;

    protected $categoryType;

    protected $categorySlug;

    public function __construct()
    {
        $this->categoryModel = request()->segment(2);
        $this->categoryType = request()->segment(3);
        $this->categorySlug = $this->categoryModel . '-' . $this->categoryType;

        view()->share([
            'categoryModel' => $this->categoryModel,
            'categoryType' => $this->categoryType,
            'categorySlug' => $this->categorySlug,
        ]);
    }

    public function index(Request $request)
    {
        session()->put('backUrl', request()->fullUrl());

        $query = QueryBuilder::for(Category::query());
        if (!$request->sort) {
            $query->sort($this->categorySlug);
        }
        $query->defaultSorts(config("admix-categories.{$this->categorySlug}.default_sort"))
            ->allowedSorts($request->sort)
            ->allowedFilters((($request->filter) ? array_keys($request->get('filter')) : []));

        if ($request->is('*/trash')) {
            $query->onlyTrashed();
        }

        $view['items'] = $query->paginate($request->get('per_page', 50));

        return view('agenciafmd/categories::index', $view);
    }

    public function create(Category $category)
    {
        $view['model'] = $category;

        return view('agenciafmd/categories::form', $view);
    }

    public function store(CategoryRequest $request)
    {
        $data = [
            'is_active' => $request->get('is_active'),
            'name' => $request->get('name'),
            'description' => $request->get('description', ''),
            'sort' => $request->sort ?? null,
        ];

        if (Category::create($data)) {
            flash('Item inserido com sucesso.', 'success');
        } else {
            flash('Falha no cadastro.', 'danger');
        }

        return ($url = session()->get('backUrl')) ? redirect($url) : redirect()->route("admix.{$this->categoryModel}.{$this->categoryType}.index");
    }

    public function show(Category $category)
    {
        $view['model'] = $category;

        return view('agenciafmd/categories::form', $view);
    }

    public function edit(Category $category)
    {
        $view['model'] = $category;

        return view('agenciafmd/categories::form', $view);
    }

    public function update(Category $category, CategoryRequest $request)
    {
        $data = [
            'is_active' => $request->get('is_active'),
            'name' => $request->get('name'),
            'description' => $request->get('description', ''),
            'sort' => $request->sort ?? null,
        ];

        if ($category->update($data)) {
            flash('Item atualizado com sucesso.', 'success');
        } else {
            flash('Falha na atualização.', 'danger');
        }

        return ($url = session()->get('backUrl')) ? redirect($url) : redirect()->route("admix.{$this->categoryModel}.{$this->categoryType}.index");
    }

    public function destroy(Category $category)
    {
        if ($category->delete()) {
            flash('Item removido com sucesso.', 'success');
        } else {
            flash('Falha na remoção.', 'danger');
        }

        return ($url = session()->get('backUrl')) ? redirect($url) : redirect()->route("admix.{$this->categoryModel}.{$this->categoryType}.index");
    }

    public function restore($id)
    {
        $category = Category::onlyTrashed()
            ->find($id);

        if (!$category) {
            flash('Item já restaurado.', 'danger');
        } elseif ($category->restore()) {
            flash('Item restaurado com sucesso.', 'success');
        } else {
            flash('Falha na restauração.', 'danger');
        }

        return ($url = session()->get('backUrl')) ? redirect($url) : redirect()->route("admix.{$this->categoryModel}.{$this->categoryType}.index");
    }

    public function batchDestroy(Request $request)
    {
        if (Category::destroy($request->get('id', []))) {
            flash('Item removido com sucesso.', 'success');
        } else {
            flash('Falha na remoção.', 'danger');
        }

        return ($url = session()->get('backUrl')) ? redirect($url) : redirect()->route("admix.{$this->categoryModel}.{$this->categoryType}.index");
    }

    public function batchRestore(Request $request)
    {
        $category = Category::onlyTrashed()
            ->whereIn('id', $request->get('id', []))
            ->restore();

        if ($category) {
            flash('Item restaurado com sucesso.', 'success');
        } else {
            flash('Falha na restauração.', 'danger');
        }

        return ($url = session()->get('backUrl')) ? redirect($url) : redirect()->route("admix.{$this->categoryModel}.{$this->categoryType}.index");
    }
}
```

### Rotas

No arquivo `packages/agenciafmd/admix-articles/src/routes/web.php` e adicione **antes** das rotas do pacote

```
use Agenciafmd\Articles\Http\Controllers\CategoryController;
use Agenciafmd\Articles\Models\Category;

if (config('admix-articles.category')) {
    Route::get('articles/categories', [CategoryController::class, 'index'])
        ->name('admix.articles.categories.index')
        ->middleware('can:view,' . Category::class);
    Route::get('articles/categories/trash', [CategoryController::class, 'index'])
        ->name('admix.articles.categories.trash')
        ->middleware('can:restore,' . Category::class);
    Route::get('articles/categories/create', [CategoryController::class, 'create'])
        ->name('admix.articles.categories.create')
        ->middleware('can:create,' . Category::class);
    Route::post('articles/categories', [CategoryController::class, 'store'])
        ->name('admix.articles.categories.store')
        ->middleware('can:create,' . Category::class);
    Route::get('articles/categories/{category}', [CategoryController::class, 'show'])
        ->name('admix.articles.categories.show')
        ->middleware('can:view,' . Category::class);
    Route::get('articles/categories/{category}/edit', [CategoryController::class, 'edit'])
        ->name('admix.articles.categories.edit')
        ->middleware('can:update,' . Category::class);
    Route::put('articles/categories/{category}', [CategoryController::class, 'update'])
        ->name('admix.articles.categories.update')
        ->middleware('can:update,' . Category::class);
    Route::delete('articles/categories/destroy/{category}', [CategoryController::class, 'destroy'])
        ->name('admix.articles.categories.destroy')
        ->middleware('can:delete,' . Category::class);
    Route::post('articles/categories/{id}/restore', [CategoryController::class, 'restore'])
        ->name('admix.articles.categories.restore')
        ->middleware('can:restore,' . Category::class);
    Route::post('articles/categories/batchDestroy', [CategoryController::class, 'batchDestroy'])
        ->name('admix.articles.categories.batchDestroy')
        ->middleware('can:delete,' . Category::class);
    Route::post('articles/categories/batchRestore', [CategoryController::class, 'batchRestore'])
        ->name('admix.articles.categories.batchRestore')
        ->middleware('can:restore,' . Category::class);
}
```

### Model
Crie o arquivo `packages/agenciafmd/admix-articles/src/Models/Category.php` e adicione

```php
<?php

namespace Agenciafmd\Articles\Models;

use Agenciafmd\Categories\Models\Category as CategoryBase;
use Database\Factories\ArticleCategoryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class Category extends CategoryBase implements Searchable
{
    use HasFactory;

    protected $table = 'categories';

    protected $attributes = [
        'type' => 'articles-categories',
    ];

    public $searchableType;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->searchableType = config('admix-categories.articles-categories.name');
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('type', function (Builder $builder) {
            $builder->where('type', 'articles-categories');
        });
    }

    public function getSearchResult(): SearchResult
    {
        return new SearchResult(
            $this,
            "{$this->name}",
            route('admix.articles.categories.edit', $this->id)
        );
    }

    public function getUrlAttribute()
    {
        return route('frontend.articles.index', [
            $this->attributes['slug'],
        ]);
    }

    public function scopeSort($query, $type = 'articles-categories')
    {
        parent::scopeSort($query, $type);
    }

    protected static function newFactory()
    {
        return ArticleCategoryFactory::new();
    }
}
```

### Relacionamento
No arquivo `packages/agenciafmd/admix-articles/src/Models/Article.php` adicione

```php
use Agenciafmd\Articles\Models\Category;

...

public function category()
{
    return $this->belongsTo(Category::class);
}
```

### Factory
Crie o arquivo `packages/agenciafmd/admix-articles/src/database/factories/ArticleCategoryFactory.php.stub` e adicione

```php
<?php

namespace Database\Factories;

use Agenciafmd\Articles\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleCategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition()
    {
        return [
            'is_active' => $this->faker->optional(0.3, 1)
                ->randomElement([0]),
            'name' => $this->faker->sentence(),
        ];
    }
}
```

### Seed
Crie o arquivo `packages/agenciafmd/admix-articles/src/database/seeds/ArticlesCategoriesTableSeeder.php.stub` e adicione

```php
<?php

namespace Database\Seeders;

use Agenciafmd\Articles\Models\Category;
use Faker\Factory;
use Illuminate\Database\Seeder;

class ArticlesCategoriesTableSeeder extends Seeder
{
    protected int $total = 10;

    public function run()
    {
        Category::withTrashed()
            ->get()->each->forceDelete();

        if (!config('admix-articles.category')) {
            return false;
        }

        if (config('admix-categories.articles-categories.items')) {
            $this->staticItems();

            return false;
        }

        $this->factoryItems();
    }

    private function factoryItems()
    {
        $this->command->getOutput()
            ->progressStart($this->total);

        $faker = Factory::create('pt_BR');

        Category::factory($this->total)
            ->create()
            ->each(function ($category) use ($faker) {
                foreach (config('upload-configs.articles-categories') as $key => $image) {
                    $fakerDir = __DIR__ . '/../faker/articles-categories/' . $key;

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

    private function staticItems()
    {
        $items = collect(config('admix-categories.articles-categories.items'));

        $this->command->getOutput()
            ->progressStart($items->count());

        $items->each(function ($item) {
            Category::create([
                'is_active' => 1,
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

### Seed na ArticleFactory

```
...
$categories = Category::pluck('id')
    ->toArray();
...
    'category_id' => $this->faker->randomElement($categories),
...
```