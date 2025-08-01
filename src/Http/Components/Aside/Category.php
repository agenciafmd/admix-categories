<?php

namespace Agenciafmd\Categories\Http\Components\Aside;

use Agenciafmd\Categories\Models\Category as CategoryModel;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\Component;

class Category extends Component
{
    public function __construct(
        public string $icon = '',
        public string $label = '',
        public string $url = '',
        public bool $active = false,
        public bool $visible = false,
    ) {}

    public function render(): View
    {
        $this->icon = __(config('admix-categories.icon'));
        $this->label = __(config('admix-categories.name'));
        $this->url = route('admix.categories.index', [
            'categoryModel' => 'products',
            'categoryType' => 'categories',
        ]);
        $this->active = request()?->currentRouteNameStartsWith('admix.categories');
        $this->visible = Gate::allows('view', CategoryModel::class);

        return view('admix::components.aside.item');
    }
}
