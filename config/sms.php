<?php

return [
    'providers' => [
        'eskiz' => [
            'class' => App\Services\Sms\Providers\EskizProvider::class,
            'base_url' => env('ESKIZ_BASE_URL', 'https://notify.eskiz.uz/api'),
            'email' => env('ESKIZ_EMAIL'),
            'password' => env('ESKIZ_PASSWORD'),
            'from' => env('ESKIZ_FROM', '4546'),
        ],
        'playmobile' => [
            'class' => App\Services\Sms\Providers\PlaymobileProvider::class,
            'base_url' => env('PLAYMOBILE_BASE_URL', 'https://send.playmobile.uz'),
            'login' => env('PLAYMOBILE_LOGIN'),
            'password' => env('PLAYMOBILE_PASSWORD'),
        ],
        'twilio' => [
            'class' => App\Services\Sms\Providers\TwilioProvider::class,
            'base_url' => env('TWILIO_BASE_URL', 'https://api.twilio.com/2010-04-01'),
            'sid' => env('TWILIO_SID'),
            'token' => env('TWILIO_TOKEN'),
            'from' => env('TWILIO_FROM'),
        ],
        'fake' => [
            'class' => App\Services\Sms\Providers\FakeProvider::class,
        ],
    ],
];
