## F&MD - Categories

![Área Administrativa](https://raw.githubusercontent.com/agenciafmd/admix-categories/v11/docs/screenshot.png "Área Administrativa")

[![Downloads](https://img.shields.io/packagist/dt/agenciafmd/admix-categories.svg?style=flat-square)](https://packagist.org/packages/agenciafmd/admix-categories)
[![Licença](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

- Categorias em qualquer lugar

### Instalação

```bash
composer require agenciafmd/admix-categories:v11.x-dev
```

### Configurações

Publique o arquivo de configuração

```
php artisan vendor:publish --tag="admix-categories:config"
```

No arquivo `config/admix-categories.php` adicione as categorias que serão utilizadas.

Os slugs são utilizados para identificar as categorias na rota.

```php
<?php

return [
    'name' => 'Categories',
    'icon' => 'category',
    'sort' => 100,
    'categories' => [
        [
            'model' => \Agenciafmd\Articles\Models\Article::class,
            'name' => 'Artigos',
            'slug' => 'articles',
            'types' => [
                [
                    'name' => 'Categorias',
                    'slug' => 'categories',
                ],
                // adicione quantos tipos forem necessários
            ],
        ],
        // adicione quantas models forem necessários
    ],
];
```

### Incorporando ao seu pacote

Vamos usar o pacote **[admix-articles](https://github.com/agenciafmd/admix-articles)** como exemplo

Mova o arquivo `/config/admix-categories.php` e para `/packages/agenciafmd/admix-articles/config/admix-categories.php`

Carregue os arquivos em `/packages/agenciafmd/admix-articles/src/Providers/ArticleServiceProvider.php`

```php
protected function loadConfigs()
{
    ...
    $this->mergeConfigFrom(__DIR__ . '/../config/admix-categories.php', 'admix-categories');
}
```

### Permissões (TODO)

No arquivo `packages/agenciafmd/admix-articles/config/gate.php` adicione antes das configurações do pacote

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

### Validação (TODO)

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

### Politicas (TODO)

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

### Registrando as politicas (TODO)

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

No arquivo `packages/agenciafmd/admix-articles/src/Http/Components/Aside/Article.php` modifique a estrutura para aceitar
as categorias

```blade
<?php

namespace Agenciafmd\Products\Http\Components\Aside;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Product extends Component
{
    public function __construct(
        public string $icon = '',
        public string $label = '',
        public bool $active = false,
        public bool $visible = false,
        public array $children = [],
    ) {}

    public function render(): View
    {
        $model = 'products';
        $types = collect(config('admix-categories.categories'))
            ->where('slug', $model)->first()['types'];
        $children = collect($types)->map(function ($item) use ($model) {
            return [
                'label' => __($item['name']),
                'url' => route('admix.categories.index', [
                    'categoryModel' => $model,
                    'categoryType' => $item['slug'],
                ]),
                'active' => request()?->is("*{$model}/{$item['slug']}*"),
                'visible' => true,
            ];
        })->toArray();

        $this->icon = __(config('local-products.icon'));
        $this->label = __(config('local-products.name'));
        $this->active = request()?->currentRouteNameStartsWith(['admix.products']) || (request()->categoryModel === $model);
        $this->visible = true;

        $this->children = [
            ...$children,
            [
                'label' => __(config('local-products.name')),
                'url' => route('admix.products.index'),
                'active' => request()?->currentRouteNameStartsWith('admix.products'),
                'visible' => true,
            ],
        ];

        return view('admix::components.aside.dropdown');
    }
}
```

### Listagem (TODO)

No arquivo `packages/agenciafmd/admix-articles/src/resources/views/index.blade.php` adicione na `@section('filters')`

```blade
@include('agenciafmd/categories::partials.form.filter', [
    'label' => config('admix-categories.articles-categories.name'),
    'type' => 'articles-categories',
    'name' => 'category_id'
])
```

### Formulário (TODO)

No arquivo `packages/agenciafmd/admix-articles/src/resources/views/form.blade.php` adicione

```blade
@include('agenciafmd/categories::partials.form.select', [
    'label' => config('admix-categories.articles-categories.name'),
    'type' => 'articles-categories',
    'name' => 'category_id',
    'required' => true
])
```

### Request (TODO)

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

### Controller (TODO)

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

### Rotas (TODO)

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

### Model (TODO)

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

### Relacionamento (TODO)

No arquivo `packages/agenciafmd/admix-articles/src/Models/Article.php` adicione

```php
use Agenciafmd\Articles\Models\Category;

...

public function category()
{
    return $this->belongsTo(Category::class);
}
```

### Factory (TODO)

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

### Seed (TODO)

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

### Seed na ArticleFactory (TODO)

```
...
$categories = Category::pluck('id')
    ->toArray();
...
    'category_id' => $this->faker->randomElement($categories),
...
```