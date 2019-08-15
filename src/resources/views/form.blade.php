@extends('agenciafmd/admix::partials.crud.form')

@section('form')
    {!! Form::bsOpen(['model' => optional($model), 'create' => route("admix.{$categoryModel}.{$categoryType}.store"), 'update' => route("admix.{$categoryModel}.{$categoryType}.update", ['category' => $model->id])]) !!}
    <div class="card-header bg-gray-lightest">
        <h3 class="card-title">
            @if(request()->is('*/create'))
                Criar
            @elseif(request()->is('*/edit'))
                Editar
            @else
                Visualizar
            @endif
            {{ config("admix-categories.{$categoryType}.name") }}
        </h3>
        <div class="card-options">
            @if(strpos(request()->route()->getName(), 'show') === false)
                @include('agenciafmd/admix::partials.btn.save')
            @endif
        </div>
    </div>
    <ul class="list-group list-group-flush">
        @if (optional($model)->id)
            {!! Form::bsText('Código', 'id', null, ['disabled' => true]) !!}
        @endif

        {!! Form::bsIsActive('Ativo', 'is_active', null, ['required']) !!}

        @if(config("admix-categories.{$categoryType}.star"))
            {{ Form::bsBoolean('Destaque', 'star', null, ['required' => true]) }}
        @endif

        {!! Form::bsText('Nome', 'name', null, ['required']) !!}

        @if(config("admix-categories.{$categoryType}.description"))
            {!! Form::bsTextarea('Descrição', 'description') !!}
        @endif

        @if(config("admix-categories.{$categoryType}.image"))
            {!! Form::bsImage('Imagem', 'image', $model, [
                'config' => config("upload-configs." . Str::singular($categoryType))
            ]) !!}
        @endif
    </ul>
    <div class="card-footer bg-gray-lightest text-right">
        <div class="d-flex">
            @include('agenciafmd/admix::partials.btn.back')

            @if(strpos(request()->route()->getName(), 'show') === false)
                @include('agenciafmd/admix::partials.btn.save')
            @endif
        </div>
    </div>
    {!! Form::close() !!}
@endsection
