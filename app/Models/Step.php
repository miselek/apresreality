<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Step extends Model
{
    protected $fillable = ['process_id', 'order', 'name', 'description', 'duration_days', 'is_auto'];

    protected function casts(): array
    {
        return [
            'is_auto' => 'boolean',
        ];
    }

    public function process(): BelongsTo
    {
        return $this->belongsTo(Process::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
