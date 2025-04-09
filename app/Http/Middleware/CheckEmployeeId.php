<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckEmployeeId
{
    public function handle(Request $request, Closure $next): Response
    {
        \Illuminate\Support\Facades\Log::info('CheckEmployeeId middleware ejecutándose', [
            'user_authenticated' => auth()->check(),
            'user_nif' => auth()->check() ? auth()->user()->nif : null
        ]);

        if (auth()->check() && empty(auth()->user()->nif)) {
            \Illuminate\Support\Facades\Log::info('Usuario sin NIF, redirigiendo a employee-id.edit');
            return redirect()->route('employee-id.edit');
        }

        return $next($request);
    }
}
