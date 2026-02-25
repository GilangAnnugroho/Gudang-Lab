<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Unit;

class Request extends Model
{
    use HasFactory;

    protected $table = 'requests';

    public const STATUS_PENDING     = 'PENDING';
    public const STATUS_APPROVED    = 'APPROVED';
    public const STATUS_REJECTED    = 'REJECTED';
    public const STATUS_DISTRIBUTED = 'DISTRIBUTED';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_APPROVED,
        self::STATUS_REJECTED,
        self::STATUS_DISTRIBUTED,
    ];

    protected $fillable = [
        'request_date',
        'status',
        'request_user_id',
        'approver_user_id',
        'unit_id',
    ];

    protected $casts = [
        'request_date' => 'date',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'request_user_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_user_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(RequestDetail::class, 'request_id');
    }
    
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'request_id');
    }

    public function scopeForUser($query, User $user)
    {
        $roleName = optional($user->role)->role_name;

        if (in_array($roleName, ['Super Admin', 'Admin Gudang', 'Kepala Lab'])) {
            return $query;
        }

        return $query->where('unit_id', $user->unit_id);
    }

    public function scopeStatus($query, ?string $status)
    {
        if ($status && in_array($status, self::STATUSES, true)) {
            $query->where('status', $status);
        }
        return $query;
    }

    public function scopeUnitId($query, ?int $unitId)
    {
        if ($unitId) {
            $query->where('unit_id', $unitId);
        }
        return $query;
    }

    public function scopeDateBetween($query, ?string $from, ?string $to)
    {
        if ($from) $query->whereDate('request_date', '>=', $from);
        if ($to)   $query->whereDate('request_date', '<=', $to);
        return $query;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING     => 'Menunggu',
            self::STATUS_APPROVED    => 'Disetujui',
            self::STATUS_REJECTED    => 'Ditolak',
            self::STATUS_DISTRIBUTED => 'Sudah Didistribusi',
            default                  => $this->status ?? '-',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING     => 'warning',
            self::STATUS_APPROVED    => 'success',
            self::STATUS_REJECTED    => 'danger',
            self::STATUS_DISTRIBUTED => 'primary',
            default                  => 'secondary',
        };
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function getIsApprovedAttribute(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function getIsDistributedAttribute(): bool
    {
        return $this->status === self::STATUS_DISTRIBUTED;
    }

    public function getHasDistributionAttribute(): bool
    {
        return ($this->transactions_count ?? 0) > 0;
    }

    public function getDistributionCountAttribute(): int
    {
        return (int) ($this->transactions_count ?? 0);
    }
}
