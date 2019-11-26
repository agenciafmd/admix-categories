<?php

namespace Agenciafmd\Categories;

use Agenciafmd\Admix\MediaTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;

class Category extends Model implements AuditableContract, HasMedia
{
    use SoftDeletes, Auditable, HasMediaTrait, MediaTrait {
        MediaTrait::registerMediaConversions insteadof HasMediaTrait;
    }

    protected $guarded = [
        'media',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->slug = Str::slug($model->name);
        });
    }

    public function scopeIsActive($query)
    {
        $query->where('is_active', 1);
    }

    public function scopeSort($query, $type = 'categories')
    {
        $sorts = default_sort(config("admix-categories.{$type}.default_sort"));

        foreach ($sorts as $sort) {
            $query->orderBy($sort['field'], $sort['direction']);
        }
    }

    public $registerMediaConversionsUsingModelInstance = true;

    public function registerMediaConversions(Media $media = null)
    {
        $fields = config('upload-configs.' . $this->attributes['type']);
        foreach ($fields as $collection => $field) {
            $conversion = $this->addMediaConversion('thumb');
            if ($field['crop']) {
                $conversion->fit(Manipulations::FIT_CROP, $field['width'], $field['height']);
            } else {
                $conversion->width($field['width'])
                    ->height($field['height']);
            }
            if (!app()->environment('local')) {
                if ($field['optimize']) {
                    $conversion->optimize();
                }
                if ($field['quality']) {
                    $conversion->quality($field['quality']);
                }
            }
            $conversion->performOnCollections($collection)
                ->keepOriginalImageFormat();
        }
    }
}
