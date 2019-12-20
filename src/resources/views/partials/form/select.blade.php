@inject('categoryService', '\Agenciafmd\Categories\Services\CategoryService')

@formSelect([$label, $name, ['' => '-'] + $categoryService->lists($type)->toArray(), null, ['required' => ($required) ?? false ]])
