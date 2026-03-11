<?php

namespace App\Exceptions;

use RuntimeException;

class SmsProviderException extends RuntimeException
{
    public function __construct(
        public readonly string $provider,
        string $message,
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct("[{$provider}] {$message}", $code, $previous);
    }
}
