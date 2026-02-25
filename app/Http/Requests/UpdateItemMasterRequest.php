<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\ItemMaster;

class UpdateItemMasterRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }

    public function rules(): array
    {
        $itemParam = $this->route('item'); 
        $itemId = $itemParam instanceof ItemMaster ? $itemParam->id : $itemParam;

        return [
            'item_code'    => ['required','string','max:50',
                Rule::unique('items_master','item_code')->ignore($itemId) 
            ],
            'item_name'    => ['required','string','max:255'],
            'base_unit'    => ['required','string','max:50'],
            'category_id'  => ['required','exists:categories,id'],
            'warnings'     => ['nullable','string'],
            'storage_temp' => ['nullable','string','max:255'],
            'size'         => ['nullable','string','max:100'],
        ];
    }
}
