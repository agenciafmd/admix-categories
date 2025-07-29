<?php

namespace Agenciafmd\Categories\Livewire\Pages\Category;

use Agenciafmd\Admix\Livewire\Pages\Base\Index as BaseIndex;
use Agenciafmd\Categories\Helper;
use Agenciafmd\Categories\Models\Category;
use Agenciafmd\Ui\LaravelLivewireTables\Columns\DeleteColumn;
use Agenciafmd\Ui\LaravelLivewireTables\Columns\EditColumn;
use Agenciafmd\Ui\LaravelLivewireTables\Columns\RestoreColumn;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\ButtonGroupColumn;

class Index extends BaseIndex
{
    protected $model = Category::class;

    protected string $indexRoute = 'admix.categories.index';

    protected string $trashRoute = 'admix.categories.trash';

    protected string $creteRoute = 'admix.categories.create';

    protected string $editRoute = 'admix.categories.edit';

    protected string $categoryModel = '';

    protected string $categoryType = '';

    protected ?string $parsedCategoryModel = null;

    public function mount(): void
    {
        $this->user = auth('admix-web')
            ->user();
        $this->isTrash = request()?->is('*/trash');

        ($this->isTrash) ? $this->authorize('restore', $this->model) : $this->authorize('view', $this->model);

        $this->setCategoryParams();
    }

    public function configure(): void
    {
        $this->setCategoryParams();

        $modelConfig = collect(config('admix-categories.categories'))->firstWhere('model', $this->parsedCategoryModel);
        $categoryName = collect($modelConfig['types'])->firstWhere('slug', $this->categoryType)['name'] ?? config('admix-categories.name');
        $this->packageName = __($categoryName);

        parent::configure();
    }

    public function builder(): Builder
    {
        return $this->model::query()
            ->where(function (Builder $builder) {
                $builder->where($builder->qualifyColumn('model'), $this->parsedCategoryModel)
                    ->where($builder->qualifyColumn('type'), $this->categoryType);
            })
            ->when($this->isTrash, function (Builder $builder) {
                $builder->onlyTrashed();
            })
            ->when(!$this->hasSorts(), function (Builder $builder) {
                $builder->sort();
            });
    }

    public function columns(): array
    {
        $actions = [];
        if ($this->isTrash) {
            if ($this->user->can('restore', $this->builder()
                ->getModel())) {
                $actions[] = RestoreColumn::make('Restore')
                    ->title(fn ($row) => __('Restore'))
                    ->location(fn ($row) => "window.Livewire.dispatchTo('" . str(static::class)
                        ->lower()
                        ->replace('\\', '.')
                        ->toString() . "', 'bulkRestore', { id: {$row->id} })")
                    ->attributes(function ($row) {
                        return [
                            'class' => 'btn ms-0 ms-md-2',
                        ];
                    });
            }
        } else {
            if ($this->user->can('update', $this->builder()
                ->getModel())) {
                $actions[] = EditColumn::make('Edit')
                    ->title(fn ($row) => __('Edit'))
                    ->location(fn ($row) => route($this->editRoute, [
                        'categoryModel' => $this->categoryModel,
                        'categoryType' => $this->categoryType,
                        'category' => $row->id,
                    ]))
                    ->attributes(function ($row) {
                        return [
                            'class' => 'btn ms-2',
                        ];
                    });
            }

            if ($this->user->can('delete', $this->builder()
                ->getModel())) {
                $actions[] = DeleteColumn::make('Delete')
                    ->title(fn ($row) => __('Delete'))
                    ->location(fn ($row) => $row->id)
                    ->attributes(function ($row) {
                        return [
                            'class' => 'btn ms-2',
                        ];
                    });
            }
        }
        $actionButtons = array_merge($this->additionalActionButtons, $actions);

        return [
            Column::make(__('admix::fields.id'), 'id')
                ->sortable()
                ->searchable(),
            Column::make(__('admix::fields.name'), 'name')
                ->sortable()
                ->searchable(),
            ...$this->additionalColumns,
            Column::make(__('admix-categories::fields.sort'), 'sort')
                ->sortable(),
            BooleanColumn::make(__('admix::fields.is_active'), 'is_active')
                ->setView('admix-ui::livewire-tables.columns.boolean')
                ->sortable()
                ->searchable(),
            ButtonGroupColumn::make('')
                ->excludeFromColumnSelect()
                ->attributes(function ($row) {
                    return [
                        'class' => 'text-end',
                    ];
                })
                ->buttons($actionButtons),
        ];
    }

    public function headerActions(): array
    {
        if ($this->indexRoute && $this->isTrash) {
            return [
                '<x-btn href="' . route($this->indexRoute, [
                    'categoryModel' => $this->categoryModel,
                    'categoryType' => $this->categoryType,
                ]) . '"
                        label="' . __('Back') . '"/>',
            ];
        }
        $actions = [];
        if ($this->creteRoute && $this->user->can('create', $this->builder()->getModel())) {
            $actions[] = '<x-btn.create href="' . route($this->creteRoute, [
                'categoryModel' => $this->categoryModel,
                'categoryType' => $this->categoryType,
            ]) . '"
                    label="' . $this->packageName . '" />';
        }
        if ($this->trashRoute && $this->user->can('restore', $this->builder()->getModel())) {
            $actions[] = '<x-btn.trash href="' . route($this->trashRoute, [
                'categoryModel' => $this->categoryModel,
                'categoryType' => $this->categoryType,
            ]) . '"
                    label="" />';
        }

        return $actions;
    }

    public function render(): View
    {
        if ($this->indexRoute) {
            session()->put('backUrl', route($this->indexRoute, [
                'categoryModel' => $this->categoryModel,
                'categoryType' => $this->categoryType,
                'table-search' => $this->search,
            ]));
        }

        $this->setupColumnSelect();
        $this->setupPagination();
        $this->setupReordering();

        return view('admix-ui::livewire-tables.datatable')
            ->with([
                'pageTitle' => $this->pageTitle,
                'headerActions' => $this->headerActions(),
                'columns' => $this->getColumns(),
                'rows' => $this->getRows(),
                'customView' => $this->customView(),
            ])
            ->extends('admix::internal')
            ->section('internal-content');
    }

    private function setCategoryParams(): void
    {
        $categoryModel = '';
        $categoryType = '';

        /* essa é uma gambeta para conseguir pegar os dados da url de fora do componente */
        $content = json_decode(request()->getContent());
        if ($content) {
            $path = json_decode($content->components[0]->snapshot)->memo->path;
            [$admix, $categoryModel, $categoryType] = explode('/', $path);
        }

        $this->categoryModel = request()->categoryModel ?? $categoryModel;
        $this->categoryType = request()->categoryType ?? $categoryType;
        $this->parsedCategoryModel = Helper::parseModel($this->categoryModel);
    }
}
