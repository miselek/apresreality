<?php

namespace App\Models;

use App\Enums\ContractStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contract extends Model
{
    protected $fillable = [
        'template_id', 'contact_id', 'data', 'status',
        'validation_result', 'verification_result',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'status' => ContractStatus::class,
            'verification_result' => 'array',
        ];
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(ContractTemplate::class, 'template_id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }
}
