<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_name' => 'required|string|max:100|unique:categories,category_name',
            'description'   => 'nullable|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'category_name' => 'Nama Kategori',
            'description'   => 'Deskripsi',
        ];
    }
}
