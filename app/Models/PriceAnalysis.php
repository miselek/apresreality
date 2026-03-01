<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceAnalysis extends Model
{
    protected $fillable = [
        'contact_id', 'address', 'area', 'property_type', 'condition',
        'floor', 'ownership', 'estimated_price', 'comparables', 'report_url',
    ];

    protected function casts(): array
    {
        return [
            'area' => 'decimal:2',
            'estimated_price' => 'decimal:2',
            'comparables' => 'array',
        ];
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }
}
