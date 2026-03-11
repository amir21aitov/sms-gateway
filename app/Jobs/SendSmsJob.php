<?php

namespace App\Jobs;

use App\Enums\SmsStatus;
use App\Exceptions\SmsProviderException;
use App\Models\SmsMessage;
use App\Services\Sms\SmsProviderFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public int $tries = 3;
    public array $backoff = [10, 60, 300];

    public function __construct(
        public readonly SmsMessage $smsMessage,
    ) {}

    public function handle(SmsProviderFactory $factory): void
    {
        $project = $this->smsMessage->project;
        $provider = $factory->make($project->provider);

        $result = $provider->send($this->smsMessage->phone, $this->smsMessage->message);

        $this->smsMessage->update([
            'status' => $result->success ? SmsStatus::SENT : SmsStatus::FAILED,
            'provider_response' => $result->providerResponse,
            'sent_at' => $result->success ? now() : null,
        ]);
    }

    public function failed(Throwable $exception): void
    {
        $response = [
            'error' => $exception->getMessage()
        ];

        if ($exception instanceof SmsProviderException) {
            $response['provider'] = $exception->provider;
        }

        $this->smsMessage->update([
            'status' => SmsStatus::FAILED,
            'provider_response' => $response,
        ]);
    }
}
