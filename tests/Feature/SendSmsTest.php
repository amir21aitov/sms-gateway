<?php

namespace Tests\Feature;

use App\Enums\SmsProvider;
use App\Enums\SmsStatus;
use App\Jobs\SendSmsJob;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SendSmsTest extends TestCase
{
    use RefreshDatabase;

    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->project = Project::create([
            'name' => 'Test Project',
            'provider' => SmsProvider::FAKE,
        ]);
    }

    public function test_send_sms_with_valid_data(): void
    {
        Queue::fake();

        $response = $this->postJson('/api/sms/send', [
            'api_key' => $this->project->api_key,
            'phones' => ['+998901234567'],
            'message' => 'Test message',
        ]);

        $response->assertStatus(202)
            ->assertJsonStructure([
                'statusCode',
                'statusDescription',
                'data',
            ]);

        $this->assertDatabaseHas('sms_messages', [
            'project_id' => $this->project->id,
            'phone' => '+998901234567',
            'message' => 'Test message',
            'status' => SmsStatus::PENDING->value,
        ]);

        Queue::assertPushed(SendSmsJob::class);
    }

    public function test_send_sms_with_multiple_phones(): void
    {
        Queue::fake();

        $phones = ['+998901234567', '+998901234568', '+998901234569'];

        $response = $this->postJson('/api/sms/send', [
            'api_key' => $this->project->api_key,
            'phones' => $phones,
            'message' => 'Bulk message',
        ]);

        $response->assertStatus(202);
        $this->assertCount(3, $this->project->smsMessages);
        Queue::assertPushed(SendSmsJob::class, 3);
    }

    public function test_send_sms_with_invalid_api_key(): void
    {
        $response = $this->postJson('/api/sms/send', [
            'api_key' => 'invalid-key',
            'phones' => ['+998901234567'],
            'message' => 'Test',
        ]);

        $response->assertStatus(401);
    }

    public function test_send_sms_without_api_key(): void
    {
        $response = $this->postJson('/api/sms/send', [
            'phones' => ['+998901234567'],
            'message' => 'Test',
        ]);

        $response->assertStatus(422);
    }

    public function test_send_sms_with_invalid_phone_format(): void
    {
        $response = $this->postJson('/api/sms/send', [
            'api_key' => $this->project->api_key,
            'phones' => ['12345', '+7901234567'],
            'message' => 'Test',
        ]);

        $response->assertStatus(422);
    }

    public function test_send_sms_with_empty_phones(): void
    {
        $response = $this->postJson('/api/sms/send', [
            'api_key' => $this->project->api_key,
            'phones' => [],
            'message' => 'Test',
        ]);

        $response->assertStatus(422);
    }

    public function test_send_sms_without_message(): void
    {
        $response = $this->postJson('/api/sms/send', [
            'api_key' => $this->project->api_key,
            'phones' => ['+998901234567'],
        ]);

        $response->assertStatus(422);
    }
}
