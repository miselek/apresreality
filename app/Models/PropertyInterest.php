<?php

namespace App\Models;

use App\Enums\InterestType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyInterest extends Model
{
    protected $fillable = ['property_id', 'contact_id', 'type', 'note', 'visited_at'];

    protected function casts(): array
    {
        return [
            'type' => InterestType::class,
            'visited_at' => 'datetime',
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
}
