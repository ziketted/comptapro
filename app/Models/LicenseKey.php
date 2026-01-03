<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LicenseKey extends Model
{
    protected $fillable = [
        'key',
        'tenant_id',
        'status',
        'activated_at',
        'expires_at',
    ];

    protected $casts = [
        'activated_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function isUnused(): bool
    {
        return $this->status === 'UNUSED';
    }

    public function isUsed(): bool
    {
        return $this->status === 'USED';
    }

    public function isExpired(): bool
    {
        return $this->status === 'EXPIRED' || ($this->expires_at && $this->expires_at->isPast());
    }
}
