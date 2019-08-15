@inject('categoryService', '\Agenciafmd\Categories\Services\CategoryService')

<h6 class="dropdown-header bg-gray-lightest p-2">{{ config("admix-categories.{$type}.name") }}</h6>
<div class="p-2">
    {{ Form::select('filter[' . Str::singular($type) . "_id" . ']', ['' => '-'] + $categoryService->lists($type)->toArray(), filter(Str::singular($type) . "_id"), [
            'class' => 'form-control form-control-sm'
        ]) }}
</div>
