@extends('agenciafmd/admix::partials.crud.form')

@section('form')
    {{ Form::bsOpen(['model' => optional($model), 'create' => route("admix.{$categoryModel}.{$categoryType}.store"), 'update' => route("admix.{$categoryModel}.{$categoryType}.update", ['category' => $model->id ?? 0])]) }}
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
            {{ Form::bsText('Código', 'id', null, ['disabled' => true]) }}
        @endif

        {{ Form::bsIsActive('Ativo', 'is_active', null, ['required']) }}

        {{ Form::bsText('Nome', 'name', null, ['required']) }}

        @if(config("admix-categories.{$categorySlug}.description"))
            {{ Form::bsTextarea('Descrição', 'description') }}
        @endif

        @foreach(config("upload-configs.{$categorySlug}") as $key => $image)
            @if($image['multiple'])
                {{ Form::bsImages($image['name'], $key, $model, ['config' => config("upload-configs.{$categorySlug}")]) }}
            @else
                {{ Form::bsImage($image['name'], $key, $model, ['config' => config("upload-configs.{$categorySlug}")]) }}
            @endif
        @endforeach

        {{ Form::bsText('Ordenação', 'sort') }}
    </ul>
    <div class="card-footer bg-gray-lightest text-right">
        <div class="d-flex">
            @include('agenciafmd/admix::partials.btn.back')

            @if(strpos(request()->route()->getName(), 'show') === false)
                @include('agenciafmd/admix::partials.btn.save')
            @endif
        </div>
    </div>
    {{ Form::close() }}
@endsection
