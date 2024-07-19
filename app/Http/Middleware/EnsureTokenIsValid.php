<?php

namespace App\Http\Middleware;

use App\Http\Controllers\HttpClientController;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class EnsureTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Storage::missing('token.json') || time() > Storage::json('token.json')['token_expired_time']) {
            $response = HttpClientController::GetToken();
            $token = ['access_token' => $response['access_token'], 'token_expired_time' => time() + $response['expires_in']];
            Storage::put('token.json', json_encode($token));
        }
        # Append token to request
        $request->token = Storage::json('token.json')['access_token'];
        return $next($request);
    }
}
