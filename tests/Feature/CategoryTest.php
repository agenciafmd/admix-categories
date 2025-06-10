<?php

use Agenciafmd\Categories\Http\Livewire\Pages\Category\Form;
use Agenciafmd\Categories\Http\Livewire\Pages\Category\Index;
use Agenciafmd\Categories\Models\Category;
use Livewire\Livewire;

it('can render index route of categories', function () {
    asAdmix()
        ->get(route('admix.categories.index'))
        ->assertOk();
});

it('can see item on index route of categories', function () {
    $model = create(Category::class);

    asAdmix()
        ->get(route('admix.categories.index'))
        ->assertOk()
        ->assertSee($model->name);
});

it('can render create route of categories', function () {
    asAdmix()
        ->get(route('admix.categories.create'))
        ->assertOk();
});

it('can insert item on create route of categories', function () {
    asAdmix();
    $model = make(Category::class);

    Livewire::test(Form::class)
        ->set('model.is_active', $model->is_active)
        ->set('model.name', $model->name)
        ->call('submit');

    test()->assertDatabaseHas(table(Category::class), [
        'name' => $model->name,
    ]);
});

it('can render and see a item on edit route of categories', function () {
    $model = create(Category::class);

    asAdmix()
        ->get(route('admix.categories.edit', $model))
        ->assertOk()
        ->assertSee($model->name);
});

it('can edit item on edit route of categories', function () {
    asAdmix();
    $model = create(Category::class);

    Livewire::test(Form::class, ['faq' => $model->id])
        ->set('model.name', $model->name . ' - edited')
        ->call('submit');

    test()->assertDatabaseHas(table(Category::class), [
        'name' => $model->name . ' - edited',
    ]);
});

it('can delete item on index route of categories', function () {
    asAdmix();
    $model = create(Category::class);

    Livewire::test(Index::class)
        ->call('bulkDelete', $model->id);

    test()->assertSoftDeleted(table(Category::class), [
        'id' => $model->id,
    ]);
});

it('can render and see a item on trash route of categories', function () {
    $model = create(Category::class);
    $model->delete();

    asAdmix()
        ->get(route('admix.categories.trash'))
        ->assertOk()
        ->assertSee($model->name);
});

it('can restore item on trash route of categories', function () {
    asAdmix();

    $model = create(Category::class);
    $model->delete();

    Livewire::test(Index::class)
        ->set('isTrash', true)
        ->call('bulkRestore', $model->id);

    test()->assertDatabaseHas(table(Category::class), [
        'id' => $model->id,
        'deleted_at' => null,
    ]);
});
