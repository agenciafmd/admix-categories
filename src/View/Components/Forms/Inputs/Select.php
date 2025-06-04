<?php

namespace Agenciafmd\Categories\View\Components\Forms\Inputs;

use Agenciafmd\Categories\Services\CategoryService;
use Illuminate\Support\Facades\View;
use Illuminate\View\Component;

class Select extends Component
{
    public string $uuid;

    public function __construct(
        public string $name = '',
        public string $label = '',
        public string $hint = '',
        public array $options = [],
        public mixed $model = null,
        public string $type = 'categories',
    ) {
        $this->uuid = '-' . str(serialize($this))
            ->pipe('md5')
            ->limit(5, '')
            ->toString();

        $options = (new CategoryService)->toSelect($model, $this->type);
        $this->options = collect($options)->map(function ($item, $key) {
            return [
                'value' => $key,
                'label' => $item,
            ];
        })->toArray();
    }

    public function render(): string|View
    {
        return <<<'HTML'
            @if($label)
                <x-form.label for="{{ $name . $uuid }}" @class(['required' => $attributes->has('required')])>
                    {{ str($label)->lower()->ucfirst() }}
                </x-form.label>
            @endif
            <select wire:model.change="{{ $name }}" 
                {{ $attributes->merge([
                        'type' => 'text',
                        'id' => $name . $uuid,
                    ])->class([
                        'form-select',
                        'is-invalid' => $errors->has($name),
                    ])
                }}
            >
            @foreach($options as $option)
                @if(!$attributes->has('multiple') && $loop->first)
                    <option value="" selected>{{ __('-') }}</option>
                @endif
                <option value="{{ $option['value'] }}" @disabled(isset($option['disabled']) && ($option['disabled']))>{{ $option['label'] }}</option>
            @endforeach
            </select>
            <x-form.error field="{{ $name }}"/>
            <x-form.hint message="{{ $hint }}"/>
        HTML;
    }
}
