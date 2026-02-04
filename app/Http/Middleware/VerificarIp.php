<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarIp
{
    
    private $ipsPermitidas = [
        'https://azariah-unbrittle-gwen.ngrok-free.dev',
        'https://roni-promodernistic-depreciatingly.ngrok-free.dev'
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ipCliente = $request->ip();

        if (!in_array($ipCliente, $this->ipsPermitidas)) {
            return response()->json([
                'error' => 'Acceso no autorizado',
                'mensaje' => 'Tu IP no está registrada como compañero en este juego',
                'ip_detectada' => $ipCliente,
                'sugerencia' => 'Contacta al administrador para registrar tu IP'
            ], 403);
        }

        return $next($request);
    }

    public function esIpPermitida(string $ip): bool
    {
        return in_array($ip, $this->ipsPermitidas);
    }

    public function obtenerIpsPermitidas(): array
    {
        return $this->ipsPermitidas;
    }
}
