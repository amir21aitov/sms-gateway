<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SmsMessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'phone' => $this->phone,
            'message' => $this->message,
            'status' => $this->status->value,
            'provider_response' => $this->provider_response,
            'created_at' => $this->created_at?->toIso8601String(),
            'sent_at' => $this->sent_at?->toIso8601String(),
        ];
    }
}
