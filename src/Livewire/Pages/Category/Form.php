<?php

namespace Agenciafmd\Categories\Livewire\Pages\Category;

use Agenciafmd\Categories\Models\Category;
use Agenciafmd\Ui\Traits\WithMediaSync;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;

class Form extends LivewireForm
{
    use WithMediaSync;

    public Category $category;

    public $model;

    public $type;

    public $my_config;

    #[Validate]
    public int $parent_id = 0;

    #[Validate]
    public bool $is_active = true;

    #[Validate]
    public string $name = '';

    #[Validate]
    public ?string $color = '';

    #[Validate]
    public ?string $description = '';

    #[Validate]
    public array $image_files = [];

    #[Validate]
    public array $image_meta = [];

    #[Validate]
    public Collection $image;

    #[Validate]
    public ?int $sort = null;

    public function setModel(Category $category, mixed $model, string $type): void
    {
        $this->category = $category;
        $this->model = $model;
        $this->type = $type;
        $this->my_config = $this->categoryConfig();
        $this->image = collect();

        if ($category->exists) {
            $this->is_active = $category->is_active;
            $this->parent_id = $category->parent_id;
            $this->name = $category->name;
            $this->color = $category->color;
            $this->description = $category->description;
            $this->image = $category->image;
            $this->image_meta = $this->image->pluck('meta')
                ->toArray();
            $this->sort = $category->sort;
        }
    }

    public function rules(): array
    {
        $rules = [
            'is_active' => [
                'boolean',
            ],
            'name' => [
                'required',
                'max:255',
            ],
            'sort' => [
                'nullable',
                'integer',
            ],
        ];

        if ($this->my_config['is_nested'] ?? false) {
            $rules['parent_id'] = [
                'integer',
            ];

            if ($this->parent_id) {
                $rules['parent_id'][] = Rule::exists('categories', 'id')
                    ->where(function (Builder $builder) {
                        $builder->where('model', $this->model)
                            ->where('type', $this->type)
                            ->where('id', '!=', $this->category->id);
                    });
            }
        }

        if ($this->my_config['has_color'] ?? false) {
            $rules['color'] = [
                'nullable',
                'string',
                'max:7',
                'hex_color',
            ];
        }

        if ($this->my_config['has_description'] ?? false) {
            $rules['description'] = [
                'nullable',
                'string',
            ];
        }

        if (!empty($this->my_config['image'])) {
            $defaultImageRules = array_merge([
                'max_size' => 1024 * 2, // 2MB
                'max_width' => 400,
                'max_height' => 400,
                'ratio' => 1,
            ], $this->my_config['image']);

            $rules = array_merge([
                'image_files.*' => [
                    'image',
                    'max:' . $defaultImageRules['max_size'],
                    Rule::dimensions()
                        ->ratio($defaultImageRules['ratio'])
                        ->maxWidth($defaultImageRules['max_width'])
                        ->maxHeight($defaultImageRules['max_height']),
                ],
                'image' => [
                    'array',
                    'min:1',
                ],
                'image_meta' => [
                    'array',
                ],
            ], $rules);
        }

        return $rules;
    }

    public function validationAttributes(): array
    {
        return [
            'is_active' => __('admix-categories::fields.is_active'),
            'name' => __('admix-categories::fields.name'),
            'parent_id' => __('admix-categories::fields.parent_id'),
            'color' => __('admix-categories::fields.color'),
            'description' => __('admix-categories::fields.description'),
            'image' => __('admix-categories::fields.image'),
            'image_files.*' => __('admix-categories::fields.image'),
            'image_meta' => __('admix-categories::fields.image'),
            'sort' => __('admix-categories::fields.sort'),
        ];
    }

    public function save(): bool
    {
        $this->validate(rules: $this->rules(), attributes: $this->validationAttributes());
        $data = $this->except(
            'image',
            'image_files',
            'image_meta',
            'category',
            'my_config',
        );
        $this->category->fill($data);

        if (!$this->category->exists) {
            $this->category->save();
        }

        $this->syncMedia($this->category, 'image');

        return $this->category->save();
    }

    private function categoryConfig()
    {
        $modelConfig = collect(config('admix-categories.categories'))->firstWhere('model', $this->model);

        return collect($modelConfig['types'])->firstWhere('slug', $this->type);
    }
}
