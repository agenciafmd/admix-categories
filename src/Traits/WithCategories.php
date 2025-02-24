<?php

namespace Agenciafmd\Categories\Traits;

use Agenciafmd\Categories\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    public function categoriesToSelect(string $type = 'categories'): array
    {
        return Category::query()
            ->where('model', self::class)
            ->where('type', $type)
            ->sort()
            ->pluck('name', 'id')
            ->toArray();
    }

    public function syncCategories(array $ids): array
    {
        return $this->categories()
            ->sync($ids);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            Category::class,
            'categoriables',
            'categoriable_id',
            'category_id',
        )->sort();
    }

    public function scopeWithAnyCategories(Builder $builder, array $ids = [], ?string $type = null): Builder
    {
        return $builder->whereHas('categories', function (Builder $builder) use ($type, $ids) {
            $builder->where('model', self::class)
                ->when($type, function ($builder) use ($type) {
                    $builder->where('type', $type);
                })
                ->when($ids, function ($builder) use ($ids) {
                    $builder->whereIn('id', $ids);
                });
        });
    }
}
