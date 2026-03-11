<?php

namespace App\Services\Sms\Contracts;

use App\Services\Sms\DTO\SmsResult;

interface SmsProviderInterface
{
    public function send(string $phone, string $message): SmsResult;
}
