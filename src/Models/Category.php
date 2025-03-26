<?php

namespace Agenciafmd\Categories\Models;

use Agenciafmd\Admix\Traits\WithScopes;
use Agenciafmd\Admix\Traits\WithSlug;
use Agenciafmd\Categories\Database\Factories\CategoryFactory;
use Agenciafmd\Categories\Observers\CategoryObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

#[ObservedBy([CategoryObserver::class])]
class Category extends Model implements AuditableContract
{
    use Auditable, HasFactory, Prunable, SoftDeletes, WithScopes, WithSlug;

    protected array $defaultSort = [
        'is_active' => 'desc',
        'name' => 'asc',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function prunable(): Builder
    {
        return static::query()->where('deleted_at', '<=', now()->subYear());
    }

    protected static function newFactory(): CategoryFactory|\Database\Factories\CategoryFactory
    {
        if (class_exists(\Database\Factories\CategoryFactory::class)) {
            return \Database\Factories\CategoryFactory::new();
        }

        return CategoryFactory::new();
    }
}
