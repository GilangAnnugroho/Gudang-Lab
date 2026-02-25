<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemMaster extends Model
{
    use HasFactory;

    protected $table = 'items_master';

    protected $fillable = [
        'item_code',
        'item_name',
        'base_unit',
        'warning_stock', 
        'warnings',
        'storage_temp',
        'size',
        'category_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public const BASE_UNITS = [
        'pak',
        'kotak',
        'dus',
        'botol',
        'pcs',
        'tube',
        'ampul',
        'roll',
        'set',
        'lembar',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function variants()
    {
        return $this->hasMany(ItemVariant::class, 'item_master_id');
    }

    public function scopeSearch($q, $s)
    {
        if (!$s) return $q;

        return $q->where(function ($qq) use ($s) {
            $qq->where('item_code', 'like', "%{$s}%")
               ->orWhere('item_name', 'like', "%{$s}%")
               ->orWhereHas('category', function ($q2) use ($s) {
                   $q2->where('category_name', 'like', "%{$s}%");
               });
        });
    }

    public function getIsReagenAttribute(): bool
    {
        $name = strtolower(optional($this->category)->category_name ?? '');
        return str_contains($name, 'reagen');
    }

    public function setBaseUnitAttribute($value)
    {
        $this->attributes['base_unit'] = is_string($value)
            ? strtolower(trim($value))
            : $value;
    }

    public function getBaseUnitLabelAttribute(): string
    {
        $unit = $this->base_unit ?: '';
        return $unit === '' ? '' : ucfirst($unit);
    }
}
