<?php

namespace App\Http\Middleware;

use App\Enums\HttpCode;
use App\Helpers\ResponseHelper;
use App\Models\Project;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->input('api_key') ?? $request->header('X-Api-Key');

        if (!$apiKey) {
            return ResponseHelper::response([
                    'message' => 'API key is required.'
                ], HttpCode::UNAUTHORIZED,
            );
        }

        $project = Project::query()->where('api_key', $apiKey)->first();

        if (! $project) {
            return ResponseHelper::response([
                'message' => 'Invalid API key.'
                ], HttpCode::UNAUTHORIZED,
            );
        }

        $request->attributes->set('project', $project);

        return $next($request);
    }
}
