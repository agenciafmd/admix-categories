@extends('agenciafmd/admix::partials.crud.form')

@section('form')
    @formModel(['model' => optional($model), 'create' => route("admix.{$categoryModel}.{$categoryType}.store"), 'update' => route("admix.{$categoryModel}.{$categoryType}.update", [($model->id) ?? 0]), 'id' => 'formCrud', 'class' => 'mb-0 card-list-group card' . ((count($errors) > 0) ? ' was-validated' : '')])
    <div class="card-header bg-gray-lightest">
        <h3 class="card-title">
            @if(request()->is('*/create'))
                Criar
            @elseif(request()->is('*/edit'))
                Editar
            @else
                Visualizar
            @endif
            {{ config("admix-categories.{$categorySlug}.name") }}
        </h3>
        <div class="card-options">
            @if(strpos(request()->route()->getName(), 'show') === false)
                @include('agenciafmd/admix::partials.btn.save')
            @endif
        </div>
    </div>
    <ul class="list-group list-group-flush">
        @if (optional($model)->id)
            @formText(['Código', 'id', null, ['disabled' => true]])
        @endif

        @formIsActive(['Ativo', 'is_active', null, ['required']])

        @formText(['Nome', 'name', null, ['required']])

        @if(config("admix-categories.{$categorySlug}.description"))
            @formTextArea(['Descrição', 'description'])
        @endif

        @foreach(config("upload-configs.{$categorySlug}") as $key => $image)
            @if($image['multiple'])
                @formImages([$image['name'], $key, $model, ['config' => config("upload-configs.{$categorySlug}")]])
            @else
                @formImage([$image['name'], $key, $model, ['config' => config("upload-configs.{$categorySlug}")]])
            @endif
        @endforeach

        @formText(['Ordenação', 'sort'])
    </ul>
    <div class="card-footer bg-gray-lightest text-right">
        <div class="d-flex">
            @include('agenciafmd/admix::partials.btn.back')

            @if(strpos(request()->route()->getName(), 'show') === false)
                @include('agenciafmd/admix::partials.btn.save')
            @endif
        </div>
    </div>
    @formClose()
@endsection
