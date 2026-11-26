<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermitirRegistroInicial
{
    public function handle(Request $request, Closure $next): Response
    {
        // Si ya existe algún usuario, impedir el registro.
        if (User::exists()) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
