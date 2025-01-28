<?php

namespace Agenciafmd\Categories\Models;

use Agenciafmd\Media\Traits\MediaTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\Models\Media;

class Category extends Model implements AuditableContract, HasMedia
{
    use Auditable, MediaTrait, SoftDeletes;

    protected $guarded = [
        'media',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function ($model) {
            $model->slug = Str::slug($model->name);
        });
    }

    public function scopeIsActive(Builder $query): void
    {
        $query->where('is_active', 1);
    }

    public function scopeSort(Builder $query, string $type = 'categories'): void
    {
        $sorts = default_sort(config("admix-categories.{$type}.default_sort"));

        foreach ($sorts as $sort) {
            if ($sort['field'] === 'sort') {
                $query->orderByRaw('ISNULL(sort), sort ASC');
            } else {
                $query->orderBy($sort['field'], $sort['direction']);
            }
        }
    }

    public $registerMediaConversionsUsingModelInstance = true;

    public function fieldsToConversion()
    {
        return config("upload-configs.{$this->attributes['type']}");
    }

    //    public function registerMediaConversions(Media $media = null)
    //    {
    //        $fields = config('upload-configs.' . $this->attributes['type']);
    //        foreach ($fields as $collection => $field) {
    //            $conversion = $this->addMediaConversion('thumb');
    //            if ($field['crop']) {
    //                $conversion->fit(Manipulations::FIT_CROP, $field['width'], $field['height']);
    //            } else {
    //                $conversion->width($field['width'])
    //                    ->height($field['height']);
    //            }
    //            if (!app()->environment('local')) {
    //                if ($field['optimize']) {
    //                    $conversion->optimize();
    //                }
    //                if ($field['quality']) {
    //                    $conversion->quality($field['quality']);
    //                }
    //            }
    //            $conversion->performOnCollections($collection)
    //                ->keepOriginalImageFormat();
    //        }
    //    }
}
