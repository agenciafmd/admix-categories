<?php

namespace Agenciafmd\Categories\Models;

use Agenciafmd\Admix\Traits\WithScopes;
use Agenciafmd\Admix\Traits\WithSlug;
use Agenciafmd\Categories\Database\Factories\CategoryFactory;
use Agenciafmd\Categories\Observers\CategoryObserver;
use Agenciafmd\Ui\Casts\AsSingleMediaLibrary;
use Agenciafmd\Ui\Traits\WithUpload;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[ObservedBy([CategoryObserver::class])]
class Category extends Model implements AuditableContract, HasMedia
{
    use Auditable, HasFactory, InteractsWithMedia, Prunable, SoftDeletes, WithScopes, WithSlug, WithUpload;

    protected array $defaultSort = [
        'is_active' => 'desc',
        'sort' => 'asc',
        'name' => 'asc',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'image' => AsSingleMediaLibrary::class,
        ];
    }

    public function hasParent(): bool
    {
        return !empty($this->parent_id);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id')
            ->orderBy('parent_id')
            ->sort();
    }

    public function parentRecursive(): BelongsTo
    {
        return $this->parent()
            ->with('parentRecursive');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')
            ->orderBy('parent_id')
            ->sort();
    }

    public function childrenRecursive(): HasMany
    {
        return $this->children()
            ->with('childrenRecursive');
    }

    public function prunable(): Builder
    {
        return static::query()
            ->where('deleted_at', '<=', now()->subYear());
    }

    protected static function newFactory(): CategoryFactory|\Database\Factories\CategoryFactory
    {
        if (class_exists(\Database\Factories\CategoryFactory::class)) {
            return \Database\Factories\CategoryFactory::new();
        }

        return CategoryFactory::new();
    }
}
