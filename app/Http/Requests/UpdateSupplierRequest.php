<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupplierRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }

    public function rules(): array
    {
        $id = $this->route('supplier')?->id ?? null;

        return [
            'supplier_name'  => 'required|string|max:100|unique:suppliers,supplier_name,' . $id,
            'contact_person' => 'nullable|string|max:100',
            'phone'          => 'nullable|string|max:20',
            'address'        => 'nullable|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'supplier_name'  => 'nama supplier',
            'contact_person' => 'kontak person',
            'phone'          => 'telepon',
            'address'        => 'alamat',
        ];
    }
}
