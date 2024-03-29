<?php

namespace App\Http\Middleware;

// use GuzzleHttp\Psr7\Response;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */ // 
    protected function redirectTo(Request $request)
    {
        if ($request->expectsJson()) {
            return null;
        }
    
        return route('login');
        // response('Unauthorized', Response::HTTP_UNAUTHORIZED)->withHeaders([
        //     'Content-Type' => 'text/plain',
        // ]);
    }
}
