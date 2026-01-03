<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyncLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'establishments_count',
        'success_count',
        'failed_count',
        'ip_address',
        'error_details',
    ];

    protected $casts = [
        'error_details' => 'array',
    ];

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function scopeByAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function getSuccessRateAttribute(): float
    {
        if ($this->establishments_count === 0) {
            return 0;
        }
        return round(($this->success_count / $this->establishments_count) * 100, 2);
    }
}
