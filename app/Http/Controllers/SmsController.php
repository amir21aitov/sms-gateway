<?php

namespace App\Http\Controllers;

use App\Enums\HttpCode;
use App\Helpers\ResponseHelper;
use App\Http\Requests\SendSmsRequest;
use App\Http\Requests\SmsHistoryRequest;
use App\Http\Resources\SmsMessageResource;
use App\Services\Sms\SmsService;
use Illuminate\Http\JsonResponse;

class SmsController extends Controller
{
    public function __construct(
        private readonly SmsService $smsService,
    ) {}

    public function send(SendSmsRequest $request): JsonResponse
    {
        $project = $request->attributes->get('project');

        $messages = $this->smsService->sendBulk(
            $project,
            $request->validated('phones'),
            $request->validated('message'),
        );

        return ResponseHelper::response(
            SmsMessageResource::collection($messages),
            HttpCode::ACCEPTED,
        );
    }

    public function history(SmsHistoryRequest $request): JsonResponse
    {
        $project = $request->attributes->get('project');

        $paginator = $this->smsService->getHistory(
            $project,
            $request->validated(),
            (int) $request->input('limit', 15),
        );

        return ResponseHelper::paginate($paginator, SmsMessageResource::class);
    }
}
