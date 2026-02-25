<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'unit_id',
        'remember_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function requestsMade(): HasMany
    {
        return $this->hasMany(Request::class, 'request_user_id');
    }

    public function requestsApproved(): HasMany
    {
        return $this->hasMany(Request::class, 'approver_user_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }

    public function getRoleNameAttribute(): string
    {
        return optional($this->role)->role_name ?? '-';
    }

    public function getUnitNameAttribute(): string
    {
        return optional($this->unit)->unit_name ?? '-';
    }

    public function isSuperAdmin(): bool
    {
        return strtolower($this->role_name) === 'super admin';
    }

    public function isAdminGudang(): bool
    {
        return strtolower($this->role_name) === 'admin gudang';
    }
}
