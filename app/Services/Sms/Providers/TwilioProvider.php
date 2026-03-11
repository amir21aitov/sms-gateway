<?php

namespace App\Services\Sms\Providers;

use App\Exceptions\SmsProviderException;
use App\Services\Sms\Contracts\SmsProviderInterface;
use App\Services\Sms\DTO\SmsResult;
use Illuminate\Support\Facades\Http;

class TwilioProvider implements SmsProviderInterface
{
    private string $baseUrl;
    private string $sid;
    private string $token;
    private string $from;

    public function __construct(array $config)
    {
        $this->baseUrl = $config['base_url'];
        $this->sid = $config['sid'];
        $this->token = $config['token'];
        $this->from = $config['from'];
    }

    public function send(string $phone, string $message): SmsResult
    {
        $response = Http::baseUrl($this->baseUrl)
            ->withBasicAuth($this->sid, $this->token)
            ->asForm()
            ->post("/Accounts/{$this->sid}/Messages.json", [
                'to' => $phone,
                'from' => $this->from,
                'dody' => $message,
            ]);

        if ($response->failed()) {
            throw new SmsProviderException('twilio', "Send failed: {$response->body()}", $response->status());
        }

        $data = $response->json();

        return SmsResult::success(
            response: $data,
            messageId: $data['sid'] ?? null,
        );
    }
}
