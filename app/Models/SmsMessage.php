<?php

namespace App\Models;

use App\Enums\SmsStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsMessage extends Model
{
    use HasFactory;
    protected $fillable = [
        'project_id',
        'phone',
        'message',
        'status',
        'provider_response',
        'sent_at',
    ];

    protected $casts = [
        'status' => SmsStatus::class,
        'provider_response' => 'array',
        'sent_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
