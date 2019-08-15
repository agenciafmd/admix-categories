@extends('agenciafmd/admix::partials.crud.index', [
    'route' => (request()->is('*/trash') ? route("admix.{$categoryModel}.{$categoryType}.trash") : route("admix.{$categoryModel}.{$categoryType}.trash"))
])

@section('title')
    @if(request()->is('*/trash'))
        Lixeira de
    @endif
    {{ config("admix-categories.{$categoryType}.name") }}
@endsection

@section('actions')
    @if(request()->is('*/trash'))
        @include('agenciafmd/admix::partials.btn.back', ['url' => route("admix.{$categoryModel}.{$categoryType}.index")])
    @else
        @can('create', '\Agenciafmd\\' . ucfirst($categoryModel) . '\\' . ucfirst(Str::singular($categoryType)))
            @include('agenciafmd/admix::partials.btn.create', ['url' => route("admix.{$categoryModel}.{$categoryType}.create"), 'label' => config("admix-categories.{$categoryType}.name")])
        @endcan
        @can('restore', '\Agenciafmd\\' . ucfirst($categoryModel) . '\\' . ucfirst(Str::singular($categoryType)))
            @include('agenciafmd/admix::partials.btn.trash', ['url' => route("admix.{$categoryModel}.{$categoryType}.trash")])
        @endcan
    @endif
@endsection

@section('batch')
    @if(request()->is('*/trash'))
        {{ Form::select('batch', ['' => 'com os selecionados', route("admix.{$categoryModel}.{$categoryType}.batchRestore") => '- restaurar'], null, ['class' => 'js-batch-select form-control custom-select']) }}
    @else
        {{ Form::select('batch', ['' => 'com os selecionados', route("admix.{$categoryModel}.{$categoryType}.batchDestroy") => '- remover'], null, ['class' => 'js-batch-select form-control custom-select']) }}
    @endif
@endsection

@section('filters')
    @if(config("admix-categories.{$categoryType}.star"))
        <h6 class="dropdown-header bg-gray-lightest p-2">Destaque</h6>
        <div class="p-2">
            {{ Form::text('filter[star]', filter('star'), [
                    'class' => 'form-control form-control-sm'
                ]) }}
        </div>
    @endif
@endsection

@section('table')
    @if($items->count() > 0)
        <div class="table-responsive">
            <table class="table table-striped table-borderless table-vcenter card-table text-nowrap">
                <thead>
                <tr>
                    <th class="w-1 d-none d-md-table-cell">&nbsp;</th>
                    <th class="w-1">{!! column_sort('#', 'id') !!}</th>
                    <th>{!! column_sort('Nome', 'name') !!}</th>
                    @if(config("admix-categories.{$categoryType}.star"))
                        <th>{!! column_sort('Destaque', 'star') !!}</th>
                    @endif
                    <th>{!! column_sort('Status', 'is_active') !!}</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $item)
                    <tr>
                        <td class="d-none d-md-table-cell">
                            <label class="mb-1 custom-control custom-checkbox">
                                <input type="checkbox" class="js-check custom-control-input"
                                       name="check[]" value="{{ $item->id }}">
                                <span class="custom-control-label">&nbsp;</span>
                            </label>
                        </td>
                        <td><span class="text-muted">{{ $item->id }}</span></td>
                        <td>{{ $item->name }}</td>
                        @if(config("admix-categories.{$categoryType}.star"))
                            <td>
                                @include('agenciafmd/admix::partials.label.star', ['star' => $item->star])
                            </td>
                        @endif
                        <td>
                            @include('agenciafmd/admix::partials.label.status', ['status' => $item->is_active])
                        </td>
                        @if(request()->is('*/trash'))
                            <td class="w-1 text-right">
                                @include('agenciafmd/admix::partials.btn.restore', ['url' => route("admix.{$categoryModel}.{$categoryType}.restore", $item->id)])
                            </td>
                        @else
                            <td class="w-1 text-center">
                                <div class="item-action dropdown">
                                    <a href="#" data-toggle="dropdown" class="icon">
                                        <i class="icon fe-more-vertical text-muted"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        @include('agenciafmd/admix::partials.btn.show', ['url' => route("admix.{$categoryModel}.{$categoryType}.show", $item->id)])
                                        @can('edit', '\Agenciafmd\\' . ucfirst($categoryModel) . '\\' . ucfirst(Str::singular($categoryType)))
                                            @include('agenciafmd/admix::partials.btn.edit', ['url' => route("admix.{$categoryModel}.{$categoryType}.edit", $item->id)])
                                        @endcan
                                        @can('delete', '\Agenciafmd\\' . ucfirst($categoryModel) . '\\' . ucfirst(Str::singular($categoryType)))
                                            @include('agenciafmd/admix::partials.btn.remove', ['url' => route("admix.{$categoryModel}.{$categoryType}.destroy", $item->id)])
                                        @endcan
                                    </div>
                                </div>
                            </td>
                        @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        {!! $items->appends(request()->except(['page']))->links() !!}
    @else
        @include('agenciafmd/admix::partials.info.not-found')
    @endif
@endsection
