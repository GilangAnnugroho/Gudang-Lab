<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockCurrent extends Model
{
    protected $table = 'stock_current';
    public $timestamps = false;

    protected $primaryKey = 'item_variant_id';
    public $incrementing = false;

    protected $fillable = [
        'item_variant_id',
        'current_quantity',
    ];

    public function variant()
    {
        return $this->belongsTo(ItemVariant::class, 'item_variant_id');
    }
}
