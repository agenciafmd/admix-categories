<?php

namespace Agenciafmd\Categories\Traits;

use Agenciafmd\Categories\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait WithCategories
{
    public static function bootWithCategories()
    {
        static::forceDeleted(function (Model $deletedModel) {
            $categories = $deletedModel->categories()
                ->pluck('id');

            $deletedModel->categories()
                ->detach($categories);
        });
    }

    public function loadCategory(string $type = 'categories')
    {
        return $this->loadCategories($type)
            ->first();
    }

    public function loadCategories(string $type = 'categories')
    {
        return $this->categories()
            ->where('type', $type)
            ->get();
    }

    public function syncCategories(array $ids): array
    {
        return $this->categories()
            ->sync($ids);
    }

    public function categories(): MorphToMany
    {
        return $this
            ->morphToMany(Category::class, 'categoriable')
            ->using(MorphPivot::class)
            ->sort();
    }
}
