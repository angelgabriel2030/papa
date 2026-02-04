<?php

namespace App\Http\Controllers;

use App\Models\Numero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class numeroController extends Controller
{
    private $equipo = [
        [
            'nombre' => 'Ernesto',
            'ip' => 'https://roni-promodernistic-depreciatingly.ngrok-free.dev',
            'ruta' => 'https://roni-promodernistic-depreciatingly.ngrok-free.dev/api/recibir-numero'
        ],
        [
            'nombre' => 'Mario',
            'ip' => 'https://roni-promodernistic-depreciatingly.ngrok-free.dev',
            'ruta' => 'https://roni-promodernistic-depreciatingly.ngrok-free.dev/api/recibir-numero'
        ],
    ];

    public function enviarNumero(Request $request)
    {
        try {
            $request->validate([
                'numero' => 'required|integer|min:0',
                'companero_destino' => 'required|integer' 
            ]);

            $numeroActual = $request->numero;
            $numeroIncrementado = $numeroActual + 1;
            $indiceCompanero = $request->companero_destino;

            if (!isset($this->companeros[$indiceCompanero])) {
                return response()->json([
                    'error' => 'Compañero no encontrado',
                    'companeros_disponibles' => count($this->compañeros)
                ], 404);
            }

            $companero = $this->companeros[$indiceCompañero];

            $registro = Numero::create([
                'numero_actual' => $numeroIncrementado,
                'enviado_por' => 'Yo',
                'recibido_por' => $equipo['nombre'],
                'ip_origen' => $request->ip(),
                'ip_destino' => $equipo['ip'],
            ]);

            $response = Http::timeout(10)->post($equipo['ruta'], [
                'numero' => $numeroIncrementado,
                'enviado_por' => 'Yo',
                'ip_origen' => $request->ip()
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'mensaje' => "Número enviado exitosamente a {$equipo['nombre']}",
                    'numero_enviado' => $numeroActual,
                    'numero_recibido_por_companero' => $numeroIncrementado,
                    'companero' => $equipo['nombre'],
                    'registro_id' => $registro->id,
                    'respuesta_companero' => $response->json()
                ], 200);
            } else {
                return response()->json([
                    'error' => 'Error al enviar el número',
                    'detalles' => $response->body(),
                    'status_code' => $response->status()
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error al enviar número: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error en el servidor',
                'mensaje' => $e->getMessage()
            ], 500);
        }
    }

    public function recibirNumero(Request $request)
    {
        try {
            $request->validate([
                'numero' => 'required|integer|min:0',
                'enviado_por' => 'nullable|string',
                'ip_origen' => 'nullable|string'
            ]);

            $numeroRecibido = $request->numero;
            $enviadoPor = $request->enviado_por ?? 'Desconocido';
            $ipOrigen = $request->ip_origen ?? $request->ip();

            $registro = Numero::create([
                'numero_actual' => $numeroRecibido,
                'enviado_por' => $enviadoPor,
                'recibido_por' => 'Yo', 
                'ip_origen' => $ipOrigen,
                'ip_destino' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'mensaje' => 'Número recibido exitosamente',
                'numero_recibido' => $numeroRecibido,
                'enviado_por' => $enviadoPor,
                'registro_id' => $registro->id,
                'timestamp' => now()->toDateTimeString()
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error al recibir número: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al procesar el número',
                'mensaje' => $e->getMessage()
            ], 500);
        }
    }

    public function iniciarJuego(Request $request)
    {
        $request->validate([
            'companero_destino' => 'required|integer'
        ]);

        return $this->enviarNumero(
            $request->merge(['numero' => 1])
        );
    }

    public function historial()
    {
        $numeros = Numero::orderBy('created_at', 'desc')
            ->take(50)
            ->get();

        return response()->json([
            'success' => true,
            'total' => $numeros->count(),
            'numeros' => $numeros
        ], 200);
    }

    public function ultimoNumero()
    {
        $ultimo = Numero::orderBy('created_at', 'desc')->first();

        if (!$ultimo) {
            return response()->json([
                'mensaje' => 'No hay numeros registrados aun',
                'sugerencia' => 'Inicia el juego con /api/iniciar-juego'
            ], 200);
        }

        return response()->json([
            'success' => true,
            'ultimo_numero' => $ultimo
        ], 200);
    }
    public function listar()
    {
        $companeros = [];
        foreach ($this->compañeros as $index => $equipo) {
            $companeros[] = [
                'indice' => $index,
                'nombre' => $companero['nombre'],
                'ip' => $companero['ip']
            ];
        }

        return response()->json([
            'success' => true,
            'total_companeros' => count($compañeros),
            'companeros' => $compañeros
        ], 200);
    }

    public function continuarJuego(Request $request)
    {
        $request->validate([
            'numero_recibido' => 'required|integer',
            'companero_destino' => 'required|integer'
        ]);

        $numeroRecibido = $request->numero_recibido;
        
        return $this->enviarNumero(
            $request->merge(['numero' => $numeroRecibido])
        );
    }
}