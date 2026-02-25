<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use App\Models\ItemBatch;

class ItemVariant extends Model
{
    use HasFactory;

    protected $table = 'item_variants';

    protected $fillable = [
        'item_master_id',
        'brand',
        'lot_number',
        'expiration_date',
    ];

    protected $casts = [
        'expiration_date' => 'date',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
    ];

    public function itemMaster()
    {
        return $this->belongsTo(ItemMaster::class, 'item_master_id');
    }

    public function stock()
    {
        return $this->hasOne(StockCurrent::class, 'item_variant_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'item_variant_id');
    }

    public function batches()
    {
        return $this->hasMany(ItemBatch::class, 'item_variant_id');
    }

    public function nearestBatch()
    {
        return $this->batches()
            ->whereNotNull('expiration_date')
            ->orderBy('expiration_date', 'asc')
            ->first();
    }

    public function getBatchesCountAttribute(): int
    {
        if (array_key_exists('batches_count', $this->attributes)) {
            return (int) $this->attributes['batches_count'];
        }

        return $this->batches()->count();
    }

    public function scopeSearch($q, $s)
    {
        if (!$s) return $q;

        return $q->where(function($qq) use ($s) {
            $qq->where('brand', 'like', "%{$s}%")
               ->orWhere('lot_number', 'like', "%{$s}%");
        });
    }

    public function getVariantLabelAttribute(): string
    {
        $brand = $this->brand ?: '-';
        $lot   = $this->lot_number ?: '-';
        $exp   = $this->expiration_date
                ? $this->expiration_date->format('d-m-Y')
                : '-';

        return "{$brand} (LOT: {$lot} / EXP: {$exp})";
    }

    public function getFefoStatusAttribute(): ?string
    {
        if (!$this->expiration_date) {
            return null; 
        }

        $today = Carbon::today();
        $exp   = $this->expiration_date->copy();
        $diffDays = $today->diffInDays($exp, false);

        if ($diffDays < 0) {
            return 'EXPIRED';
        }

        $diffMonths = $today->diffInMonths($exp);

        if ($diffMonths < 3) {
            return 'MERAH';     
        }

        if ($diffMonths <= 12) {
            return 'KUNING';  
        }

        return 'HIJAU';      
    }

    public function getFefoBadgeClassAttribute(): string
    {
        switch ($this->fefo_status) {
            case 'EXPIRED':
            case 'MERAH':
                return 'danger';  
            case 'KUNING':
                return 'warning'; 
            case 'HIJAU':
                return 'success'; 
            default:
                return 'secondary';
        }
    }

    public function getFefoLabelTextAttribute(): string
    {
        switch ($this->fefo_status) {
            case 'EXPIRED':
                return 'KADALUARSA';
            case 'MERAH':
                return '< 3 bulan';
            case 'KUNING':
                return '3–12 bulan';
            case 'HIJAU':
                return '> 1 tahun';
            default:
                return 'Tidak ada exp';
        }
    }
}
