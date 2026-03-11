<?php

namespace App\Services\Sms;

use App\Enums\SmsProvider;
use App\Services\Sms\Contracts\SmsProviderInterface;
use InvalidArgumentException;

class SmsProviderFactory
{
    public function make(SmsProvider $provider): SmsProviderInterface
    {
        $config = config("sms.providers.{$provider->value}");

        if (! $config || ! isset($config['class'])) {
            throw new InvalidArgumentException("SMS provider [{$provider->value}] is not configured.");
        }

        return app($config['class']);
    }
}
