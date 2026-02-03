<?php

namespace App\Http\Controllers;

use App\Models\Numero;
use Illuminate\Http\Request;

class NumeroController extends Controller
{

    public function enviar(Request $request)
    {
        $request->validate([
            'numero'    => 'required|integer|min:1',
            'ip_recibe' => 'required|string|ip',
        ]);

        $ipEnvia = $request->attributes->get('ip_real');

        if ($ipEnvia === $request->ip_recibe) {
            return response()->json([
                'success' => false,
                'message' => 'No puedes enviar el numero',
            ], 422);
        }

        $pendiente = Numero::where('estado', 'pendiente')->first();

        if ($pendiente) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede enviar',
            ], 422);
        }

        $ultimo = Numero::where('estado', 'recibido')
                        ->orderBy('id', 'desc')
                        ->first();

        $acumulado = $ultimo ? $ultimo->numero_acumulado + $request->numero : $request->numero;

        $numero = Numero::create([
            'ip_envia'         => $ipEnvia,
            'ip_recibe'        => $request->ip_recibe,
            'numero_enviado'   => $request->numero,
            'numero_acumulado' => $acumulado,
            'estado'           => 'pendiente',
        ]);

        return response()->json([
            'success'          => true,
            'message'          => 'Numero enviado.',
            'numero_enviado'   => $numero->numero_enviado,
            'numero_acumulado' => $numero->numero_acumulado,
            'enviado_a'        => $numero->ip_recibe,
        ], 201);
    }

    public function recibir(Request $request)
    {
        $ipRecibe = $request->attributes->get('ip_real');

        $numero = Numero::pendientePara($ipRecibe)->first();

        if (!$numero) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes numero pendiente',
            ], 200);
        }

        $numero->marcarRecibido();

        return response()->json([
            'success'          => true,
            'message'          => 'Numero recibido',
            'numero_enviado'   => $numero->numero_enviado,
            'numero_acumulado' => $numero->numero_acumulado,
            'enviado_por'      => $numero->ip_envia,
        ], 200);
    }

    public function historial()
    {
        $historial = Numero::orderBy('id', 'asc')
                           ->get()
                           ->map(function ($numero) {
                               return [
                                   'id'               => $numero->id,
                                   'ip_envia'         => $numero->ip_envia,
                                   'ip_recibe'        => $numero->ip_recibe,
                                   'numero_enviado'   => $numero->numero_enviado,
                                   'numero_acumulado' => $numero->numero_acumulado,
                                   'estado'           => $numero->estado,
                               ];
                           });

        return response()->json([
            'success'   => true,
            'historial' => $historial,
        ], 200);
    }
}