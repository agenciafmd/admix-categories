<?php

namespace Agenciafmd\Categories\Services;

use Agenciafmd\Categories\Models\Category;

class CategoryService
{
    public function lists(string $type)
    {
        return Category::where('type', $type)
            ->isActive()
            ->sort($type)
            ->pluck('name', 'id');
    }
}
