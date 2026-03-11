<?php

namespace App\Http\Middleware;

use App\Enums\HttpCode;
use App\Helpers\ResponseHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateAdminToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return ResponseHelper::response([
                'message' => 'Access token is required.'
                ], HttpCode::UNAUTHORIZED,
            );
        }

        if ($token !== config('app.admin_token')) {
            return ResponseHelper::response([
                'message' => 'Invalid access token.'
                ], HttpCode::UNAUTHORIZED,
            );
        }

        return $next($request);
    }
}
