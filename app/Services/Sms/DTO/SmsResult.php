<?php

namespace App\Services\Sms\DTO;

final readonly class SmsResult
{
    public function __construct(
        public bool $success,
        public array $providerResponse,
        public ?string $messageId = null,
    ) {}

    public static function success(array $response, ?string $messageId = null): self
    {
        return new self(
            success: true,
            providerResponse: $response,
            messageId: $messageId,
        );
    }

    public static function failure(array $response): self
    {
        return new self(
            success: false,
            providerResponse: $response,
        );
    }
}
