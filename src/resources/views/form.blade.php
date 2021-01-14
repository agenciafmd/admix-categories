@extends('agenciafmd/admix::partials.crud.form')

@section('form')
    {{ Form::bsOpen(['model' => optional($model), 'create' => route("admix.{$categoryModel}.{$categoryType}.store"), 'update' => route("admix.{$categoryModel}.{$categoryType}.update", ['category' => $model->id ?? 0])]) }}
    <div class="card-header bg-gray-lightest">
        <h3 class="card-title">
            @if(request()->is('*/create'))
                Criar
            @elseif(request()->is('*/edit'))
                Editar
            @endif
            {{ config("admix-categories.{$categorySlug}.name") }}
        </h3>
        <div class="card-options">
            @include('agenciafmd/admix::partials.btn.save')
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

        @foreach(config("upload-configs.{$categorySlug}") as $field => $upload)
            @if($upload['multiple'])
                {{ Form::bsImages($upload['label'], $field, $model, ['config' => $upload['sources'][0]]) }}
            @else
                {{ Form::bsImage($upload['label'], $field, $model, ['config' => $upload['sources'][0]]) }}
            @endif
        @endforeach

        {{ Form::bsText('Ordenação', 'sort') }}
    </ul>
    <div class="card-footer bg-gray-lightest text-right">
        <div class="d-flex">
            @include('agenciafmd/admix::partials.btn.back')
            @include('agenciafmd/admix::partials.btn.save')
        </div>
    </div>
    {{ Form::close() }}
@endsection
