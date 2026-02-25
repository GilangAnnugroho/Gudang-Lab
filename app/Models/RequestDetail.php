<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestDetail extends Model
{
    protected $table = 'request_details';

    protected $fillable = [
        'request_id',
        'item_master_id',
        'item_variant_id',    
        'requested_quantity',
        'distributed_quantity',
        'notes',
    ];

    protected $casts = [
        'requested_quantity'   => 'integer',
        'distributed_quantity' => 'integer',
        'created_at'           => 'datetime',
        'updated_at'           => 'datetime',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class, 'request_id');
    }

    public function itemMaster(): BelongsTo
    {
        return $this->belongsTo(ItemMaster::class, 'item_master_id');
    }

    public function itemVariant(): BelongsTo
    {
        return $this->belongsTo(ItemVariant::class, 'item_variant_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ItemVariant::class, 'item_variant_id');
    }
}
