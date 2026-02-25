<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Transaction extends Model
{
    protected $fillable = [
        'type',
        'trans_date',
        'doc_no',
        'item_variant_id',
        'quantity',
        'supplier_id',
        'unit_id',
        'note',
        'brand',
        'invoice_no',
        'price',
        'tax_amount',
        'total_amount',
        'lot_number',
        'expiration_date',
        'payment_status',
        'storage_condition',
        'package_size',
        'request_id',
        'batch_id',
    ];

    protected $casts = [
        'trans_date'      => 'date',
        'expiration_date' => 'date',
        'quantity'        => 'integer',
        'price'           => 'decimal:2',
        'tax_amount'      => 'decimal:2',
        'total_amount'    => 'decimal:2',
    ];

    public function variant()
    {
        return $this->belongsTo(ItemVariant::class, 'item_variant_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function request()
    {
        return $this->belongsTo(\App\Models\Request::class, 'request_id');
    }

    public function batch()
    {
        return $this->belongsTo(ItemBatch::class, 'batch_id');
    }

    public function getTypeBadgeAttribute(): string
    {
        return $this->type === 'MASUK' ? 'success' : 'danger';
    }

    public function getItemNameAttribute(): string
    {
        return optional(optional($this->variant)->itemMaster)->item_name ?? '-';
    }

    public function getItemCodeAttribute(): string
    {
        return optional(optional($this->variant)->itemMaster)->item_code ?? '-';
    }

    public function getBaseUnitAttribute(): string
    {
        return optional(optional($this->variant)->itemMaster)->base_unit ?? '-';
    }

    public function getBrandAttribute(): string
    {
        $raw = $this->attributes['brand'] ?? null;

        if ($raw !== null && $raw !== '') {
            return $raw;
        }

        return optional($this->variant)->brand ?? '-';
    }

    public function getLotAttribute(): string
    {
        $rawTx = $this->attributes['lot_number'] ?? null;

        if ($rawTx !== null && trim($rawTx) !== '') {
            return $rawTx;
        }

        $batchLot = optional($this->batch)->lot_number;
        if ($batchLot !== null && trim($batchLot) !== '') {
            return $batchLot;
        }

        $variantLot = optional($this->variant)->lot_number;
        if ($variantLot !== null && trim($variantLot) !== '') {
            return $variantLot;
        }

        return '—';
    }

    public function getExpAttribute(): ?Carbon
    {
        if ($this->expiration_date instanceof Carbon) {
            return $this->expiration_date;
        }

        if (!empty($this->expiration_date)) {
            return Carbon::parse($this->expiration_date);
        }

        $batchExp = optional($this->batch)->expiration_date;
        if ($batchExp instanceof Carbon) {
            return $batchExp;
        }
        if (!empty($batchExp)) {
            return Carbon::parse($batchExp);
        }

        $expVariant = optional($this->variant)->expiration_date;
        if ($expVariant instanceof Carbon) {
            return $expVariant;
        }
        if (!empty($expVariant)) {
            return Carbon::parse($expVariant);
        }

        return null;
    }

    public function getPackageSizeAttribute(): ?string
    {
        $raw = $this->attributes['package_size'] ?? null;

        if ($raw !== null && $raw !== '') {
            return $raw;
        }

        return optional($this->variant)->package_size ?? null;
    }

    public function getCategoryAttribute(): string
    {
        $variant     = $this->variant;
        $itemMaster  = $variant ? $variant->itemMaster : null;
        $category    = $itemMaster ? $itemMaster->category : null;

        return $category?->category_name ?? '-';
    }

    public function getUnitNameAttribute(): string
    {
        return optional($this->unit)->unit_name ?? '-';
    }

    public function getSupplierNameAttribute(): string
    {
        return optional($this->supplier)->supplier_name ?? '-';
    }

    public function getHasRequestAttribute(): bool
    {
        return !is_null($this->request_id);
    }

    public function scopeType(Builder $q, ?string $type): Builder
    {
        return $type ? $q->where('type', strtoupper($type)) : $q;
    }

    public function scopeSearch(Builder $q, ?string $s): Builder
    {
        if (!$s) return $q;

        return $q->where(function ($qq) use ($s) {
            $qq->where('doc_no', 'like', "%$s%")
               ->orWhere('note', 'like', "%$s%")
               ->orWhereHas('variant.itemMaster', function ($q3) use ($s) {
                    $q3->where('item_code','like',"%$s%")
                       ->orWhere('item_name','like',"%$s%")
                       ->orWhereHas('category', function ($qc) use ($s) {
                           $qc->where('category_name', 'like', "%$s%");
                       });
               })
               ->orWhereHas('variant', function ($q4) use ($s) {
                    $q4->where('brand','like',"%$s%")
                       ->orWhere('lot_number','like',"%$s%");
               })
               ->orWhereHas('supplier', function ($qs) use ($s) {
                    $qs->where('supplier_name', 'like', "%$s%");
               })
               ->orWhereHas('unit', function ($qu) use ($s) {
                    $qu->where('unit_name', 'like', "%$s%");
               });
        });
    }

    public function scopeDateBetween(Builder $q, ?string $from, ?string $to): Builder
    {
        if ($from) $q->whereDate('trans_date', '>=', $from);
        if ($to)   $q->whereDate('trans_date', '<=', $to);
        return $q;
    }
}
