<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidarIp
{
   
    protected array $ipsPermitidas = [
    ];

    public function handle(Request $request, Closure $next)
    {
        $ip = $this->obtenerIpReal($request);

        if (!empty($this->ipsPermitidas) && !in_array($ip, $this->ipsPermitidas)) {
            return response()->json([
                'success' => false,
                'message' => 'IP no autorizada',
                'ip'      => $ip,
            ], 403);
        }

        $request->attributes->set('ip_real', $ip);

        return $next($request);
    }

    
    private function obtenerIpReal(Request $request): string
    {
        $headers = [
        ];

        foreach ($headers as $header) {
            $ip = $request->header($header);
            if ($ip) {
                $ip = explode(',', $ip)[0];
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return $request->ip();
    }
}
