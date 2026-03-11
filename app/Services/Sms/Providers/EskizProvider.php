<?php

namespace App\Services\Sms\Providers;

use App\Exceptions\SmsProviderException;
use App\Services\Sms\Contracts\SmsProviderInterface;
use App\Services\Sms\DTO\SmsResult;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class EskizProvider implements SmsProviderInterface
{
    private string $baseUrl;
    private string $email;
    private string $password;
    private string $from;

    public function __construct(array $config)
    {
        $this->baseUrl = $config['base_url'];
        $this->email = $config['email'];
        $this->password = $config['password'];
        $this->from = $config['from'] ?? '4546';
    }

    public function send(string $phone, string $message): SmsResult
    {
        $token = $this->getToken();

        $response = Http::baseUrl($this->baseUrl)
            ->withToken($token)
            ->acceptJson()
            ->post('/message/sms/send', [
                'mobile_phone' => $phone,
                'message' => $message,
                'from' => $this->from,
            ]);

        if ($response->failed()) {
            throw new SmsProviderException('eskiz', "Send failed: {$response->body()}", $response->status());
        }

        $data = $response->json();

        return SmsResult::success(
            response: $data,
            messageId: $data['id'] ?? null,
        );
    }

    private function getToken(): string
    {
        return Cache::remember('eskiz_auth_token', 3500, function () {
            $response = Http::baseUrl($this->baseUrl)
                ->acceptJson()
                ->post('/auth/login', [
                    'email' => $this->email,
                    'password' => $this->password,
                ]);

            if ($response->failed()) {
                throw new SmsProviderException('eskiz', "Auth failed: {$response->body()}", $response->status());
            }

            return $response->json('data.token');
        });
    }
}
