<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'device_token',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'device_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAgent(): bool
    {
        return $this->role === 'agent';
    }

    public function establishments()
    {
        return $this->hasMany(Establishment::class, 'agent_id');
    }

    public function syncLogs()
    {
        return $this->hasMany(SyncLog::class, 'agent_id');
    }

    public function scopeAgents($query)
    {
        return $query->where('role', 'agent');
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getTodayEstablishmentsCountAttribute(): int
    {
        return $this->establishments()
            ->whereDate('created_at', today())
            ->count();
    }

    public function getTotalEstablishmentsCountAttribute(): int
    {
        return $this->establishments()->count();
    }
}
