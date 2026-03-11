<?php

namespace App\Services\Sms;

use App\Enums\SmsStatus;
use App\Jobs\SendSmsJob;
use App\Models\Project;
use App\Models\SmsMessage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SmsService
{
    public function sendBulk(Project $project, array $phones, string $message): Collection
    {
        $messages = collect();

        foreach ($phones as $phone) {
            $smsMessage = $project->smsMessages()->create([
                'phone' => $phone,
                'message' => $message,
                'status' => SmsStatus::PENDING,
            ]);

            SendSmsJob::dispatch($smsMessage);
            $messages->push($smsMessage);
        }

        return $messages;
    }

    public function getHistory(Project $project, array $filters, int $limit = 15): LengthAwarePaginator
    {
        $query = $project->smsMessages()->latest();

        if (! empty($filters['status'])) {
            $query->where('status', SmsStatus::from($filters['status']));
        }

        if (! empty($filters['phone'])) {
            $query->where('phone', $filters['phone']);
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->paginate($limit);
    }
}
