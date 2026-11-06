<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Exception\RequestException;

class EmployeeAppApiMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $request->headers->set('authorization',  $request->header('token'));
            if(Auth::guard('api')->check()) {
                return $next($request);
            } else {
                return errorResponse( 'You are not authenticated to this service', 500, null);
            }
        } catch (RequestException $e) {
            return errorResponse($e->getMessage(), 500, null);
        }

    }
}
