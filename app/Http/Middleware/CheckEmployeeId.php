<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckEmployeeId
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && empty(auth()->user()->nif)) {
            return redirect()->route('employee-id.edit');
        }

        return $next($request);
    }
}
