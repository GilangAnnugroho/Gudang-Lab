<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Request as RequestModel; 

class Unit extends Model
{
    use HasFactory;

    protected $table = 'units';

    protected $fillable = ['unit_name'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function requests(): HasMany
    {
        return $this->hasMany(RequestModel::class, 'unit_id');
    }

    public function destinationTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'unit_id');
    }

    public function scopeSearch($q, ?string $s)
    {
        if (!$s) return $q;
        return $q->where('unit_name', 'like', "%{$s}%");
    }
}
