<?php

namespace Agenciafmd\Categories\Services;

use Agenciafmd\Categories\Models\Category;
use Illuminate\Support\Collection;

class CategoryService
{
    public function toSelect($model, string $type = 'categories'): array
    {
        $categories = Category::query()
            ->with('childrenRecursive')
            ->where('parent_id', 0)
            ->where('categories.model', $model)
            ->where('categories.type', $type)
            ->sort()
            ->get();

        return $this->recursive($categories)
            ->mapWithKeys(function ($item) {
                $data = explode('|', $item);

                return [
                    $data[0] => (($data[2] > 0) ? str_repeat('|--- ', (int) $data[2]) : '') . $data[1],
                ];
            })
            ->toArray();
    }

    private function recursive($categories, $level = 0): Collection
    {
        $data = [];
        foreach ($categories as $category) {
            $data[] = "{$category->id}|{$category->name}|{$level}";
            if ($category->childrenRecursive->count() > 0) {
                $level++;
                $data[] = $this->recursive($category->childrenRecursive, $level);
                $level--;
            }
        }

        return collect($data)
            ->flatten();
    }
}
