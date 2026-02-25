<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = [
        'category_name',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(ItemMaster::class, 'category_id');
    }

    public function scopeSearch($query, ?string $term)
    {
        return $term
            ? $query->where('category_name', 'like', "%{$term}%")
            : $query;
    }

    public function getSlugAttribute(): string
    {
        $name = strtolower($this->category_name ?? '');
        $slug = preg_replace('/[^a-z0-9]+/i', '-', $name);
        return trim($slug, '-') ?: '';
    }
}
