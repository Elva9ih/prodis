<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Establishment extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'barcode',
        'agent_id',
        'type',
        'name',
        'owner_name',
        'phone_country_code',
        'phone_number',
        'phones_json',
        'whatsapp_country_code',
        'whatsapp_number',
        'remarks',
        'photo',
        'photos_json',
        'latitude',
        'longitude',
        'city',
        'location_accuracy',
        'captured_at',
        'synced_at',
    ];

    // Location fields are GUARDED - cannot be mass-assigned after creation
    protected $guarded = [];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'location_accuracy' => 'decimal:2',
        'captured_at' => 'datetime',
        'synced_at' => 'datetime',
        'phones_json' => 'array',
        'photos_json' => 'array',
    ];

    // Prevent location updates after creation
    public static function boot()
    {
        parent::boot();

        static::creating(function ($establishment) {
            // Generate unique 6-digit barcode if not provided
            if (!$establishment->barcode) {
                $establishment->barcode = static::generateUniqueBarcode();
            }
        });

        static::updating(function ($establishment) {
            // Prevent location changes on update
            if ($establishment->isDirty(['latitude', 'longitude', 'location_accuracy', 'captured_at'])) {
                throw new \Exception('Location data cannot be modified after creation.');
            }
        });
    }

    /**
     * Generate a unique 6-digit barcode
     */
    protected static function generateUniqueBarcode(): string
    {
        do {
            // Generate random 6-digit number (100000 to 999999)
            $barcode = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        } while (static::where('barcode', $barcode)->exists());

        return $barcode;
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function answers()
    {
        return $this->hasMany(EstablishmentAnswer::class);
    }

    public function scopeClients($query)
    {
        return $query->where('type', 'client');
    }

    public function scopeFournisseurs($query)
    {
        return $query->where('type', 'fournisseur');
    }

    public function scopeByAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function getFullPhoneAttribute(): string
    {
        return $this->phone_country_code . $this->phone_number;
    }

    public function getFullWhatsappAttribute(): ?string
    {
        if (!$this->whatsapp_number) {
            return null;
        }
        return $this->whatsapp_country_code . $this->whatsapp_number;
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->photo) {
            return null;
        }
        return asset('storage/establishments/' . $this->photo);
    }

    public function getCityAttribute($value): ?string
    {
        if (!$value) {
            return null;
        }

        // Check if it's JSON (multi-language)
        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            // Get current locale
            $locale = app()->getLocale();

            // Return city name in current locale, with fallbacks
            return $decoded[$locale]
                ?? $decoded['fr']
                ?? $decoded['en']
                ?? $decoded['ar']
                ?? array_values($decoded)[0]
                ?? null;
        }

        // Return as is if it's a plain string (old data)
        return $value;
    }

    public function getMapDataAttribute(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'type' => $this->type,
            'name' => $this->name,
            'owner_name' => $this->owner_name,
            'lat' => (float) $this->latitude,
            'lng' => (float) $this->longitude,
            'agent' => $this->agent->name ?? 'Unknown',
            'created_at' => $this->created_at->format('Y-m-d H:i'),
        ];
    }
}
