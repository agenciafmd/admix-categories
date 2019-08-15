<?php

namespace Agenciafmd\Categories\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function rules()
    {
        return [
            'is_active' => 'required|boolean',
            'name' => 'nullable|max:150',
        ];
    }

    public function attributes()
    {
        return [
            'is_active' => 'ativo',
            'star' => 'destaque',
            'name' => 'nome',
            'description' => 'descrição',
        ];
    }

    public function authorize()
    {
        return true;
    }
}
