<?php

namespace Agenciafmd\Categories\Traits;

use Agenciafmd\Categories\Models\Category;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Categorizable
{
    public function categories(): MorphToMany
    {
        return $this->morphToMany(Category::class, 'model', 'categorizables')
            ->withTimestamps();
    }

    public function syncCategory($category, bool $detaching = true): static
    {
        $this->categories()->sync([$category], $detaching);

        return $this;
    }

    public function attachCategory($category)
    {
        return $this->syncCategory($category);
    }
}
