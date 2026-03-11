<?php

namespace App\Services\Sms\Providers;

use App\Services\Sms\Contracts\SmsProviderInterface;
use App\Services\Sms\DTO\SmsResult;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FakeProvider implements SmsProviderInterface
{
    public function send(string $phone, string $message): SmsResult
    {
        $messageId = Str::uuid()->toString();

        Log::info('FakeProvider: SMS sent', [
            'message_id' => $messageId,
            'phone' => $phone,
            'message' => $message,
        ]);

        return SmsResult::success(
            response: [
                'fake' => true,
                'message_id' => $messageId
            ],
            messageId: $messageId,
        );
    }
}
