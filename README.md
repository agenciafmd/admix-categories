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

Mova o arquivo `config/admix-categories.php` e para `packages/agenciafmd/admix-articles/config/admix-categories.php`

Carregue os arquivos em `packages/agenciafmd/admix-articles/src/Providers/ArticleServiceProvider.php`

```php
protected function loadConfigs()
{
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

### Preparando o formulário

No arquivo `packages/agenciafmd/admix-articles/src/Livewire/Pages/Article/Form.php`

Declare o campo, usando o valor que temos no `type` no **singular** para "hasOne" e no **plural** para "hasMany".

Vamos usar `category` para o `hasOne` e `tags` para o `hasMany`

```php
#[Validate]
public int $category = 0;

#[Validate]
public array $tags = [];
```

Alimente os campos no método `setModel()`

```php
public function setModel(Article $article): void
{
    $this->article = $article;
    if ($article->exists) {
        //...
        $this->category = $article
            ->loadCategory()
            ?->id ?? 0;
        $this->tags = $article
            ->loadCategories('tags')
            ->pluck('id')
            ->toArray();
    }
}
```

Valide os campos no método `rules()`

```php
'category' => [
    'required',
    'integer',
    Rule::exists('categories', 'id')
        ->where(function (Builder $builder) {
            $builder->where('model', Article::class)
                ->where('type', 'categories');
        }),
],
'tags' => [
    'required',
    'array',
    Rule::exists('categories', 'id')
        ->where(function (Builder $builder) {
        $builder->where('model', Article::class)
            ->where('type', 'tags');
    }),
],
```

Adicione os campos no método `validationAttributes()`

```php
'category' => __('admix-articles::fields.category'),
'tags' => __('admix-articles::fields.tags'),
```

Faça o sync no método `save()`

```php
if (!$this->article->exists) {
    $this->article->save();
}

$this->article->syncCategories([$this->category, ...$this->tags]);

return $this->article->save();
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

```php
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
        $model = 'articles';
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

        $this->icon = __(config('admix-articles.icon'));
        $this->label = __(config('admix-articles.name'));
        $this->active = request()?->currentRouteNameStartsWith(['admix.articles']) || (request()->categoryModel === $model);
        $this->visible = true;

        $this->children = [
            ...$children,
            [
                'label' => __(config('admix-articles.name')),
                'url' => route('admix.articles.index'),
                'active' => request()?->currentRouteNameStartsWith('admix.articles'),
                'visible' => true,
            ],
        ];

        return view('admix::components.aside.dropdown');
    }
}
```

### Listagem

No arquivo `packages/agenciafmd/admix-articles/src/Livewire/Pages/Article/Index.php`

Traga o `builder` da `BaseIndex` e faça
o [Eager Loading](https://laravel.com/docs/11.x/eloquent-relationships#eager-loading) da categoria

```php
public function builder(): Builder
{
return $this->model::query()
    ->with(['categories'])
    ->when($this->isTrash, function (Builder $builder) {
        $builder->onlyTrashed();
    })
    ->when(!$this->hasSorts(), function (Builder $builder) {
        $builder->sort();
    });
}
```

Monte o `filters` e adicione o filtro da categoria

```php
public function filters(): array
{
    $this->setAdditionalFilters([
        SelectFilter::make(__('admix-articles::fields.category'), 'category')
            ->options(['' => __('-'), ...(new Product)->categoriesToSelect()])
            ->filter(static function (Builder $builder, string $value) {
                $builder->whereHas('categories', function ($builder) use ($value) {
                    $builder->where($builder->qualifyColumn('model'), Product::class)
                        ->where($builder->qualifyColumn('type'), 'categories')
                        ->where($builder->qualifyColumn('id'), $value);
                });
            }),
        MultiSelectFilter::make(__('admix-articles::fields.tags'), 'tags')
            ->options((new Product)->categoriesToSelect('tags'))
            ->filter(static function (Builder $builder, array $values) {
                $builder->whereHas('categories', function ($builder) use ($values) {
                    $builder->where($builder->qualifyColumn('model'), Product::class)
                        ->where($builder->qualifyColumn('type'), 'tags')
                        ->whereIn($builder->qualifyColumn('id'), $values);
                });
            }),
    ]);

    return parent::filters();
}
```

Monte o `columns` e adicione a coluna da categoria

```php
public function columns(): array
{
    $this->setAdditionalColumns([
        Column::make(__('admix-articles::fields.category'))
            ->label(
                fn ($row, Column $column) => $row->categories
                    ->where('type', 'categories')
                    ->first()
                    ?->name
            )
            ->sortable()
            ->searchable(function (Builder $builder, $value) {
                $builder->orWhereHas('categories', function ($builder) use ($value) {
                    $builder->where($builder->qualifyColumn('name'), 'like', '%' . $value . '%');
                });
            }),
        Column::make(__('admix-articles::fields.tags'))
            ->label(
                fn ($row, Column $column) => str($row->categories
                    ->where('type', 'tags')
                    ->pluck('name')?->implode(', '))->limit(40)
            )
            ->sortable()
            ->searchable(function (Builder $builder, $value) {
                $builder->orWhereHas('categories', function ($builder) use ($value) {
                    $builder->where($builder->qualifyColumn('name'), 'like', '%' . $value . '%');
                });
            }),
    ]);

    return parent::columns();
}
```

### Formulário

No arquivo `packages/agenciafmd/admix-articles/resources/views/pages/article/form.blade.php` use o componente
`<x-categories::form.select .../>`

```blade
<div class="col-md-6 mb-3">
    <x-categories::form.select
            name="form.category"
            :label="__('admix-categories::fields.category')"
            :model=\Agenciafmd\Articles\Models\Article::class
    />
</div>
<div class="col-md-6 mb-3">
    <x-categories::form.select
            name="form.tags"
            :label="__('admix-categories::fields.tags')"
            :model=\Agenciafmd\Articles\Models\Article::class
            type="tags"
            multiple
    />
</div>
```

### Model

No arquivo `packages/agenciafmd/admix-articles/src/Models/Article.php` e adicione

```php
<?php

use Agenciafmd\Categories\Traits\WithCategories;

class Article extends Model
{
    use WithCategories;
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