<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class StockOpname extends Model

{
    protected $guarded = ['id'];

    protected $casts = [
        'opname_date' => 'date',
    ];

    public function variant()
    {
        return $this->belongsTo(ItemVariant::class, 'item_variant_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}