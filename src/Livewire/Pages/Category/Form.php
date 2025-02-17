<?php

namespace Agenciafmd\Categories\Livewire\Pages\Category;

use Agenciafmd\Categories\Models\Category;
use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;

class Form extends LivewireForm
{
    public Category $category;

    public $model;

    public $type;

    #[Validate]
    public bool $is_active = true;

    #[Validate]
    public string $name = '';

    public function setModel(Category $category, mixed $model, string $type): void
    {
        $this->category = $category;
        $this->model = $model;
        $this->type = $type;
        if ($category->exists) {
            $this->is_active = $category->is_active;
            $this->name = $category->name;
        }
    }

    public function rules(): array
    {
        return [
            'is_active' => [
                'boolean',
            ],
            'name' => [
                'required',
                'max:255',
            ],
        ];
    }

    public function validationAttributes(): array
    {
        return [
            'is_active' => __('admix-categories::fields.is_active'),
            'name' => __('admix-categories::fields.name'),
        ];
    }

    public function save(): bool
    {
        $this->validate(rules: $this->rules(), attributes: $this->validationAttributes());
        $this->category->fill($this->except('category'));

        return $this->category->save();
    }
}
