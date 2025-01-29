<?php

namespace Agenciafmd\Categories\Livewire\Pages\Category;

use Agenciafmd\Categories\Models\Category;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Livewire\Component as LivewireComponent;
use Livewire\Features\SupportRedirects\Redirector;

class Component extends LivewireComponent
{
    use AuthorizesRequests;

    public Form $form;

    public Category $category;

    public function mount(Category $category): void
    {
        ($category->exists) ? $this->authorize('update', Category::class) : $this->authorize('create', Category::class);

        $this->category = $category;
        $this->form->setModel($category);
    }

    public function submit(): null|Redirector|RedirectResponse
    {
        try {
            if ($this->form->save()) {
                flash(($this->category->exists) ? __('crud.success.save') : __('crud.success.store'), 'success');
            } else {
                flash(__('crud.error.save'), 'error');
            }

            return redirect()->to(session()->get('backUrl') ?: route('admix.categories.index'));
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (Exception $exception) {
            $this->dispatch(event: 'toast', level: 'danger', message: $exception->getMessage());
        }

        return null;
    }

    public function render(): View
    {
        return view('local-categories::pages.category.form')
            ->extends('admix::internal')
            ->section('internal-content');
    }
}
