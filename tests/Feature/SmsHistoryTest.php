<?php

namespace Tests\Feature;

use App\Enums\SmsProvider;
use App\Enums\SmsStatus;
use App\Models\Project;
use App\Models\SmsMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmsHistoryTest extends TestCase
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

    public function test_history_returns_paginated_results(): void
    {
        SmsMessage::factory()->count(20)->create([
            'project_id' => $this->project->id,
        ]);

        $response = $this->getJson('/api/sms/history?' . http_build_query([
            'api_key' => $this->project->api_key,
            'per_page' => 10,
        ]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'statusCode',
                'data' => [
                    'pagination' => ['current', 'previous', 'next', 'perPage', 'totalPage', 'totalItem'],
                    'list',
                ],
            ])
            ->assertJsonPath('data.pagination.totalItem', 20)
            ->assertJsonPath('data.pagination.perPage', 10);
    }

    public function test_history_filter_by_status(): void
    {
        SmsMessage::factory()->count(5)->create([
            'project_id' => $this->project->id,
            'status' => SmsStatus::SENT,
        ]);
        SmsMessage::factory()->count(3)->create([
            'project_id' => $this->project->id,
            'status' => SmsStatus::FAILED,
        ]);

        $response = $this->getJson('/api/sms/history?' . http_build_query([
            'api_key' => $this->project->api_key,
            'status' => 'sent',
        ]));

        $response->assertStatus(200)
            ->assertJsonPath('data.pagination.totalItem', 5);
    }

    public function test_history_filter_by_phone(): void
    {
        SmsMessage::factory()->count(3)->create([
            'project_id' => $this->project->id,
            'phone' => '+998901234567',
        ]);
        SmsMessage::factory()->count(2)->create([
            'project_id' => $this->project->id,
            'phone' => '+998901234568',
        ]);

        $response = $this->getJson('/api/sms/history?' . http_build_query([
            'api_key' => $this->project->api_key,
            'phone' => '+998901234567',
        ]));

        $response->assertStatus(200)
            ->assertJsonPath('data.pagination.totalItem', 3);
    }

    public function test_history_only_shows_own_project_messages(): void
    {
        $otherProject = Project::create([
            'name' => 'Other Project',
            'provider' => SmsProvider::FAKE,
        ]);

        SmsMessage::factory()->count(5)->create([
            'project_id' => $this->project->id,
        ]);
        SmsMessage::factory()->count(3)->create([
            'project_id' => $otherProject->id,
        ]);

        $response = $this->getJson('/api/sms/history?' . http_build_query([
            'api_key' => $this->project->api_key,
        ]));

        $response->assertStatus(200)
            ->assertJsonPath('data.pagination.totalItem', 5);
    }

    public function test_history_with_invalid_api_key(): void
    {
        $response = $this->getJson('/api/sms/history?' . http_build_query([
            'api_key' => 'invalid',
        ]));

        $response->assertStatus(401);
    }
}
