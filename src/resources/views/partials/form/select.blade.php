@inject('categoryService', '\Agenciafmd\Categories\Services\CategoryService')

{!! Form::bsSelect($label, $name, ['' => '-'] + $categoryService->lists($type)->toArray(), null, ['required' => ($required) ?? false ]) !!}
