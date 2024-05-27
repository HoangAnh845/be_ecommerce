<?php

namespace App\Http\Middleware;

// use GuzzleHttp\Psr7\Response;

use App\Core\ResponseService;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */ // 
    protected function redirectTo(Request $request)
    {
        if (!$request->expectsJson()) {
            return route('login');
        }
        // return ;
    }
}
