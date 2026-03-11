<?php

namespace App\Models;

use App\Enums\SmsProvider;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'description',
        'api_key',
        'provider',
    ];

    protected $casts = [
        'provider' => SmsProvider::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (Project $project) {
            if (empty($project->api_key)) {
                $project->api_key = Str::random(64);
            }
        });
    }

    public function smsMessages(): HasMany
    {
        return $this->hasMany(SmsMessage::class);
    }
}
