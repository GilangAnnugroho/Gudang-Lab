<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'unit_name' => 'required|string|max:100|unique:units,unit_name',
        ];
    }

    public function attributes(): array
    {
        return [
            'unit_name' => 'nama unit',
        ];
    }
}
