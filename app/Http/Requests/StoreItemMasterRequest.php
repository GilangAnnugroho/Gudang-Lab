<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemMasterRequest extends FormRequest
{

    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'item_code'    => 'required|string|max:50|unique:items_master,item_code',
            'item_name'    => 'required|string|max:255', 
            'base_unit'    => 'required|string|max:50',  
            'category_id'  => 'required|exists:categories,id', 
            'warnings'     => 'nullable|string',
            'storage_temp' => 'nullable|string|max:255', 
            'size'         => 'nullable|string|max:100', 
        ];
    }

    public function attributes(): array
    {
        return [
            'item_code'    => 'Kode Item',
            'item_name'    => 'Nama Item',
            'base_unit'    => 'Satuan Dasar',
            'category_id'  => 'Kategori',
            'warnings'     => 'Tanda Peringatan',
            'storage_temp' => 'Suhu Penyimpanan',
            'size'         => 'Ukuran',
        ];
    }
}
