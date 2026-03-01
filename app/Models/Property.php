<?php

namespace App\Models;

use App\Enums\PropertyType;
use App\Enums\PropertyStatus;
use App\Enums\PropertyPriceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'address', 'city', 'zip', 'gps_lat', 'gps_lng',
        'property_type', 'disposition', 'area', 'land_area',
        'price', 'price_type', 'commission_percent', 'commission_amount',
        'ad_budget', 'ad_spent', 'description', 'status',
        'contact_id', 'price_analysis_id', 'notes',
        'published_at', 'sold_at',
    ];

    protected function casts(): array
    {
        return [
            'property_type' => PropertyType::class,
            'status' => PropertyStatus::class,
            'price_type' => PropertyPriceType::class,
            'area' => 'decimal:2',
            'land_area' => 'decimal:2',
            'price' => 'decimal:2',
            'commission_percent' => 'decimal:2',
            'commission_amount' => 'decimal:2',
            'ad_budget' => 'decimal:2',
            'ad_spent' => 'decimal:2',
            'gps_lat' => 'decimal:7',
            'gps_lng' => 'decimal:7',
            'published_at' => 'datetime',
            'sold_at' => 'datetime',
        ];
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function priceAnalysis(): BelongsTo
    {
        return $this->belongsTo(PriceAnalysis::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(PropertyPhoto::class)->orderBy('order');
    }

    public function interests(): HasMany
    {
        return $this->hasMany(PropertyInterest::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(PropertyEvent::class)->orderBy('starts_at');
    }

    public function getProgressAttribute(): int
    {
        return match ($this->status) {
            PropertyStatus::Nabor => 0,
            PropertyStatus::Priprava => 14,
            PropertyStatus::Inzerce => 28,
            PropertyStatus::Prohlidky => 43,
            PropertyStatus::Rezervace => 57,
            PropertyStatus::Smlouva => 71,
            PropertyStatus::Prodano => 100,
            PropertyStatus::Archiv => 100,
            default => 0,
        };
    }

    public function getDaysOnMarketAttribute(): ?int
    {
        if (!$this->published_at) return null;
        $end = $this->sold_at ?? now();
        return (int) $this->published_at->diffInDays($end);
    }

    public function getPrimaryPhotoAttribute(): ?PropertyPhoto
    {
        return $this->photos->firstWhere('is_primary', true) ?? $this->photos->first();
    }

    public function getCommissionComputedAttribute(): ?float
    {
        if ($this->commission_amount) return (float) $this->commission_amount;
        if ($this->price && $this->commission_percent) {
            return round((float) $this->price * (float) $this->commission_percent / 100, 2);
        }
        return null;
    }

    public function getAdRemainingAttribute(): ?float
    {
        if (!$this->ad_budget) return null;
        return round((float) $this->ad_budget - (float) $this->ad_spent, 2);
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['prodano', 'archiv']);
    }
}
