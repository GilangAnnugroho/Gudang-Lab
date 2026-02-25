<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $id = $this->route('unit')?->id ?? null;

        return [
            'unit_name' => 'required|string|max:100|unique:units,unit_name,' . $id,
        ];
    }

    public function attributes(): array
    {
        return [
            'unit_name' => 'nama unit',
        ];
    }
}
