<?php

namespace App\Services\Sms\Providers;

use App\Exceptions\SmsProviderException;
use App\Services\Sms\Contracts\SmsProviderInterface;
use App\Services\Sms\DTO\SmsResult;
use Illuminate\Support\Facades\Http;

class PlaymobileProvider implements SmsProviderInterface
{
    private string $baseUrl;
    private string $login;
    private string $password;

    public function __construct(array $config)
    {
        $this->baseUrl = $config['base_url'];
        $this->login = $config['login'];
        $this->password = $config['password'];
    }

    public function send(string $phone, string $message): SmsResult
    {
        $response = Http::baseUrl($this->baseUrl)
            ->withBasicAuth($this->login, $this->password)
            ->acceptJson()
            ->post('/broker-api/send', [
                'messages' => [
                    [
                        'recipient' => $phone,
                        'message-id' => uniqid('pm_'),
                        'sms' => [
                            'originator' => '3700',
                            'content' => ['text' => $message],
                        ],
                    ],
                ],
            ]);

        if ($response->failed()) {
            throw new SmsProviderException('playmobile', "Send failed: {$response->body()}", $response->status());
        }

        $data = $response->json();

        return SmsResult::success(
            response: $data,
            messageId: $data['message-id'] ?? null,
        );
    }
}
