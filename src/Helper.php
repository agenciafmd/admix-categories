<?php

namespace Agenciafmd\Categories;

class Helper
{
    public static function parseModel(string $slug): ?string
    {
        $item = collect(config('admix-categories.categories'))->where('slug', $slug)->first();
        if (!$item) {
            return null;
        }

        return $item['model'];
    }

    public static function allowedTypes(): array
    {
        return collect(config('admix-categories.categories'))
            ->pluck('types')
            ->flatten(1)
            ->pluck('slug')
            ->unique()
            ->values()
            ->toArray();
    }
}
