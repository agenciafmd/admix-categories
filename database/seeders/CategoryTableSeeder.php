<?php

namespace Agenciafmd\Categories\Database\Seeders;

use Agenciafmd\Categories\Models\Category;
use Illuminate\Database\Seeder;

class CategoryTableSeeder extends Seeder
{
    protected int $total = 100;

    public function run(): void
    {
        Category::query()
            ->truncate();

        $this->command->getOutput()
            ->progressStart($this->total);

        collect(range(1, $this->total))
            ->each(function () {
                Category::factory()
                    ->create();

                $this->command->getOutput()
                    ->progressAdvance();
            });

        $this->command->getOutput()
            ->progressFinish();
    }
}
