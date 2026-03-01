<?php

namespace App\Models;

use App\Enums\TaskPriority;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $fillable = [
        'contact_id', 'step_id', 'title', 'due_date', 'priority', 'is_done', 'is_auto',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'priority' => TaskPriority::class,
            'is_done' => 'boolean',
            'is_auto' => 'boolean',
        ];
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(Step::class);
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('due_date', today())->where('is_done', false);
    }

    public function scopeTomorrow(Builder $query): Builder
    {
        return $query->whereDate('due_date', today()->addDay())->where('is_done', false);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->whereDate('due_date', '>', today()->addDay())->where('is_done', false);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->whereDate('due_date', '<', today())->where('is_done', false);
    }

    public function scopeDone(Builder $query): Builder
    {
        return $query->where('is_done', true);
    }
}
