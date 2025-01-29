<?php

namespace Agenciafmd\Categories\Models;

use Agenciafmd\Admix\Traits\WithScopes;
use Agenciafmd\Admix\Traits\WithSlug;
use Agenciafmd\Categories\Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Category extends Model implements AuditableContract
{
    use Auditable, HasFactory, Prunable, SoftDeletes, WithScopes, WithSlug;

    protected $guarded = [
        //
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected array $defaultSort = [
        'is_active' => 'desc',
        'name' => 'asc',
    ];

    public function entries(string $class): MorphToMany
    {
        return $this->morphedByMany($class, 'model', 'categorizables', 'model_id', 'category_id');
    }

    public function prunable(): Builder
    {
        return self::where('deleted_at', '<=', now()->subYear());
    }

    protected static function newFactory(): CategoryFactory|\Database\Factories\CategoryFactory
    {
        if (class_exists(\Database\Factories\CategoryFactory::class)) {
            return \Database\Factories\CategoryFactory::new();
        }

        return CategoryFactory::new();
    }
}
