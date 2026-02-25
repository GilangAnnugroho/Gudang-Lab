<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\ItemMaster;
use App\Models\ItemVariant;

class UpdateItemVariantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'item_master_id'  => ['required', 'exists:items_master,id'],
            'brand'           => ['required', 'string', 'max:100'],
            'lot_number'      => ['nullable', 'string', 'max:100'],
            'expiration_date' => ['nullable', 'date'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            $current  = $this->route('itemVariant');               
            $ignoreId = is_object($current) ? $current->getKey() : $current;

            $master   = ItemMaster::with('category')->find($this->input('item_master_id'));

            if ($master && $master->is_reagen) {
                if (!trim((string)$this->input('lot_number'))) {
                    $v->errors()->add('lot_number', 'Untuk kategori Reagen, nomor LOT wajib diisi.');
                }
                if (!trim((string)$this->input('expiration_date'))) {
                    $v->errors()->add('expiration_date', 'Untuk kategori Reagen, tanggal kedaluwarsa wajib diisi.');
                }
            }

            $exists = ItemVariant::query()
                ->where('item_master_id', $this->input('item_master_id'))
                ->where('brand',           $this->input('brand'))
                ->where('lot_number',      $this->input('lot_number'))
                ->where('expiration_date', $this->input('expiration_date'))
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists();

            if ($exists) {
                $v->errors()->add('brand', 'Kombinasi item / brand / lot / kedaluwarsa sudah ada.');
            }
        });
    }

    public function attributes(): array
    {
        return [
            'item_master_id'  => 'item',
            'brand'           => 'merek',
            'lot_number'      => 'lot/batch',
            'expiration_date' => 'tanggal kedaluwarsa',
        ];
    }
}
