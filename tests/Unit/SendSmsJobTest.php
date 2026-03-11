<?php

namespace Tests\Unit;

use App\Enums\SmsProvider;
use App\Enums\SmsStatus;
use App\Exceptions\SmsProviderException;
use App\Jobs\SendSmsJob;
use App\Models\Project;
use App\Models\SmsMessage;
use App\Services\Sms\Contracts\SmsProviderInterface;
use App\Services\Sms\DTO\SmsResult;
use App\Services\Sms\SmsProviderFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SendSmsJobTest extends TestCase
{
    use RefreshDatabase;

    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->project = Project::create([
            'name' => 'Test',
            'provider' => SmsProvider::FAKE,
        ]);
    }

    public function test_job_updates_status_to_sent_on_success(): void
    {
        $smsMessage = SmsMessage::create([
            'project_id' => $this->project->id,
            'phone' => '+998901234567',
            'message' => 'Test',
            'status' => SmsStatus::PENDING,
        ]);

        $mockProvider = $this->createMock(SmsProviderInterface::class);
        $mockProvider->method('send')->willReturn(
            SmsResult::success(['id' => '123'], '123')
        );

        $mockFactory = $this->createMock(SmsProviderFactory::class);
        $mockFactory->method('make')->willReturn($mockProvider);

        $job = new SendSmsJob($smsMessage);
        $job->handle($mockFactory);

        $smsMessage->refresh();
        $this->assertEquals(SmsStatus::SENT, $smsMessage->status);
        $this->assertNotNull($smsMessage->sent_at);
        $this->assertEquals(['id' => '123'], $smsMessage->provider_response);
    }

    public function test_job_marks_as_failed_on_exception(): void
    {
        $smsMessage = SmsMessage::create([
            'project_id' => $this->project->id,
            'phone' => '+998901234567',
            'message' => 'Test',
            'status' => SmsStatus::PENDING,
        ]);

        $exception = new SmsProviderException('fake', 'Connection failed');

        $job = new SendSmsJob($smsMessage);
        $job->failed($exception);

        $smsMessage->refresh();
        $this->assertEquals(SmsStatus::FAILED, $smsMessage->status);
        $this->assertEquals('fake', $smsMessage->provider_response['provider']);
    }
}
