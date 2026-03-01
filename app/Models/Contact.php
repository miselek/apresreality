<?php

namespace App\Models;

use App\Enums\ContactSource;
use App\Enums\ContactStatus;
use App\Enums\ContactTag;
use App\Enums\ContactType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    protected $fillable = [
        'name', 'phone', 'email', 'source', 'type', 'status',
        'tag', 'process_id', 'current_step', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'source' => ContactSource::class,
            'type' => ContactType::class,
            'status' => ContactStatus::class,
            'tag' => ContactTag::class,
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

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class)->latest();
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function priceAnalyses(): HasMany
    {
        return $this->hasMany(PriceAnalysis::class);
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    public function propertyInterests(): HasMany
    {
        return $this->hasMany(PropertyInterest::class);
    }

    public function propertyEvents(): HasMany
    {
        return $this->hasMany(PropertyEvent::class);
    }

    public function currentStepModel(): ?Step
    {
        if (!$this->process_id || !$this->current_step) {
            return null;
        }

        return Step::where('process_id', $this->process_id)
            ->where('order', $this->current_step)
            ->first();
    }

    public function getProgressAttribute(): ?int
    {
        if (!$this->process_id || !$this->current_step) {
            return null;
        }

        $totalSteps = $this->process?->steps()->count() ?? 0;
        if ($totalSteps === 0) return null;

        return (int) round(($this->current_step / $totalSteps) * 100);
    }
}
