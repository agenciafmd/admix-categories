<?php

namespace Agenciafmd\Categories\Traits;

use Agenciafmd\Categories\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;

trait WithCategories
{
    public static function bootWithCategories()
    {
        static::forceDeleted(static function (Model $deletedModel) {
            //            $categories = $deletedModel->categories()
            //                ->pluck('id');
            //
            //            $deletedModel->categories()
            //                ->detach($categories);

            // TODO: não está funcionando
            //            $deletedModel->categories()
            //                ->where('categories.model', self::class)
            //                ->get()->each(function ($category) {
            //                    $category->pivot->delete();
            //                });
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
            ->where('categories.model', self::class)
            ->where('categories.type', $type)
            ->get();
    }

    public function syncCategory(int $id, string $type = 'categories'): array
    {
        $ids = Arr::wrap($id);

        return $this->syncCategories($ids, $type);
    }

    public function syncCategories(array $ids, string $type = 'categories'): array
    {
        $this->categories()
            ->where('categories.model', self::class)
            ->where('categories.type', $type)
            ->get()
            ->each(function ($category) {
                $category->pivot->delete();
            });

        $this->categories()
            ->where('categories.model', self::class)
            ->where('categories.type', $type)
            ->attach($ids, [
                'categoriable_type' => self::class,
                'type' => $type,
            ]);

        return $ids;
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            Category::class,
            'categoriables',
            'categoriable_id',
            'category_id',
        )
            ->withPivot([
                'categoriable_type',
                'type',
            ])
            ->sort();
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
