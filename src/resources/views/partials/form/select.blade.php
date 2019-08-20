@inject('categoryService', '\Agenciafmd\Categories\Services\CategoryService')

{!! Form::bsSelect(config("admix-categories.{$type}.name"), $name, ['' => '-'] + $categoryService->lists($type)->toArray(), null, ['required' => ($required) ?? false ]) !!}
