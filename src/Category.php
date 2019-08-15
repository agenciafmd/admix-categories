<?php

namespace Agenciafmd\Categories;

use Agenciafmd\Admix\MediaTrait;
use Illuminate\Support\Str;
use Spatie\Sluggable\HasSlug;
use OwenIt\Auditing\Auditable;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Category extends Model implements AuditableContract, HasMedia
{
    use SoftDeletes, Auditable, HasSlug, HasMediaTrait, MediaTrait {
        MediaTrait::registerMediaConversions insteadof HasMediaTrait;
    }

    protected $guarded = [
        'media'
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function scopeIsActive($query)
    {
        $query->where('is_active', 1);
    }

    public function scopeSort($query, $type = 'tags')
    {
        $sorts = default_sort(config("local-tags.{$type}.default_sort"));

        foreach ($sorts as $sort) {
            $query->orderBy($sort['field'], $sort['direction']);
        }
    }

    public function fieldsToConvertion($type = 'tags')
    {
        if (request()->segment(3)) {
            $type = Str::singular(request()->segment(3));
        }

        return config("upload-configs.{$type}");
    }
}
