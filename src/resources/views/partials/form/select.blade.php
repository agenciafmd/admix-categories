@inject('categoryService', '\Agenciafmd\Categories\Services\CategoryService')

{!! Form::bsSelect(config("admix-categories.{$type}.name"), Str::singular($type) . "_id", ['' => '-'] + $categoryService->lists($type)->toArray(), null, ['required' => ($required) ?? false ]) !!}
