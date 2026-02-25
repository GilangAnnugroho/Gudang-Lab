<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\ItemVariant;
use App\Models\ItemMaster;
use App\Models\Transaction;
use Carbon\Carbon;

class ItemBatch extends Model
{
    use HasFactory;

    protected $table = 'item_batches';

    protected $fillable = [
        'item_variant_id',
        'lot_number',
        'expiration_date',
        'quantity_in',
        'quantity_out',
        'current_quantity',
    ];

    protected $casts = [
        'expiration_date' => 'date',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
    ];

    public function variant()
    {
        return $this->belongsTo(ItemVariant::class, 'item_variant_id');
    }

    public function item()
    {
        return $this->hasOneThrough(
            ItemMaster::class,
            ItemVariant::class,
            'id',              
            'id',              
            'item_variant_id',
            'item_master_id'  
        );
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'batch_id');
    }

    public function getQuantityInAttribute(): int
    {
        $batchId = $this->attributes['id'] ?? $this->getKey();
        if (!$batchId) {
            return 0;
        }

        return (int) Transaction::where('batch_id', $batchId)
            ->where('type', 'MASUK')
            ->sum('quantity');
    }

    public function getQuantityOutAttribute(): int
    {
        $batchId = $this->attributes['id'] ?? $this->getKey();
        if (!$batchId) {
            return 0;
        }

        return (int) Transaction::where('batch_id', $batchId)
            ->where('type', 'KELUAR')
            ->sum('quantity');
    }

    public function getCurrentQuantityAttribute(): int
    {
        $in  = (int) $this->quantity_in;   
        $out = (int) $this->quantity_out;  

        return max(0, $in - $out);
    }

    public function getComputedCurrentQuantityAttribute(): int
    {
        return $this->current_quantity;
    }

    public function getFefoLabelAttribute(): string
    {
        if (!$this->expiration_date) {
            return 'Tanpa Exp';
        }

        $today = Carbon::today();
        $exp   = $this->expiration_date->copy();

        if ($exp->lt($today)) {
            return 'EXPIRED';
        }

        $months = $today->diffInMonths($exp);

        if ($months < 3) {
            return 'Merah (< 3 bln)';
        } elseif ($months <= 12) {
            return 'Kuning (3–12 bln)';
        }

        return 'Hijau (> 12 bln)';
    }

    public function getFefoBadgeClassAttribute(): string
    {
        //Jika tidak ada tanggal exp -> abu-abu
        if (!$this->expiration_date) {
            return 'secondary';
        }

        $today = Carbon::today();
        $exp   = $this->expiration_date->copy();

        //Jika sudah expired -> MERAH (danger)
        if ($exp->lt($today)) {
            return 'danger';
        }

        $months = $today->diffInMonths($exp);

        //Jika sisa < 3 bulan -> MERAH (danger)
        if ($months < 3) {
            return 'danger';   
        } 
        
        //Jika sisa 3 s/d 12 bulan -> KUNING (warning)
        elseif ($months <= 12) {
            return 'warning';  
        }

        //Sisanya (> 12 bulan) -> HIJAU (success)
        return 'success';
    }
}
