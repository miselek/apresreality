<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyEvent extends Model
{
    protected $fillable = [
        'property_id', 'contact_id', 'title', 'description',
        'type', 'starts_at', 'ends_at', 'location', 'is_completed', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_completed' => 'boolean',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('starts_at', '>=', now())->where('is_completed', false)->orderBy('starts_at');
    }

    public function scopePast($query)
    {
        return $query->where(function ($q) {
            $q->where('starts_at', '<', now())->orWhere('is_completed', true);
        })->orderByDesc('starts_at');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('starts_at', today())->where('is_completed', false);
    }
}
