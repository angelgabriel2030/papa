<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarIPs
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    private array $ipsPermitidas = [
        'https://roni-promodernistic-depreciatingly.ngrok-free.dev',

    ];
    public function handle(Request $request, Closure $next): Response
    {
        $ipCliente = $request->ip();

        if (!in_array($ipCliente, $this->ipsPermitidas)) {
            return response()->json([
                'Acceso denegado: IP no autorizada.',
                'IP proporcionada: ' . $ipCliente
            ], 403);
        }

        return $next($request);
    }
}