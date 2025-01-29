<?php

namespace Agenciafmd\Categories\Livewire\Pages\Category;

use Agenciafmd\Admix\Livewire\Pages\Base\Index as BaseIndex;
use Agenciafmd\Categories\Models\Category;

class Index extends BaseIndex
{
    protected $model = Category::class;

    protected string $indexRoute = 'admix.categories.index';

    protected string $trashRoute = 'admix.categories.trash';

    protected string $creteRoute = 'admix.categories.create';

    protected string $editRoute = 'admix.categories.edit';

    public function configure(): void
    {
        $this->packageName = __(config('local-categories.name'));

        parent::configure();
    }
}