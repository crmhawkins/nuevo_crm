<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dominios\Dominio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CrmClienteController extends Controller
{
    /**
     * Obtener información del cliente asociado a un dominio
     * 
     * Este endpoint recibe un dominio y devuelve la información del cliente
     * asociado a ese dominio en el formato especificado.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function obtenerClientePorDominio(Request $request)
    {
        // Validar entrada
        $validator = Validator::make($request->all(), [
            'dominio' => 'required|string|max:255',
        ], [
            'dominio.required' => 'El dominio es obligatorio.',
            'dominio.string' => 'El dominio debe ser una cadena de texto.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
            ], 422);
        }

        $dominioNombre = $request->input('dominio');
        
        Log::info('=== INICIO obtenerClientePorDominio ===', [
            'dominio' => $dominioNombre,
            'ip' => $request->ip(),
        ]);

        try {
            // Limpiar el dominio (eliminar espacios y convertir a minúsculas)
            $dominioNombreLimpio = strtolower(trim($dominioNombre));
            
            Log::info('Buscando dominio', [
                'dominio_original' => $dominioNombre,
                'dominio_limpio' => $dominioNombreLimpio,
            ]);
            
            // Buscar dominio en la base de datos (case-insensitive)
            $dominio = Dominio::with('cliente')
                ->whereRaw('LOWER(dominio) = ?', [strtolower($dominioNombreLimpio)])
                ->first();

            if (!$dominio) {
                // Intentar búsqueda sin www
                $dominioSinWww = preg_replace('/^www\./', '', $dominioNombreLimpio);
                if ($dominioSinWww !== $dominioNombreLimpio) {
                    $dominio = Dominio::with('cliente')
                        ->whereRaw('LOWER(dominio) = ?', [strtolower($dominioSinWww)])
                        ->first();
                }
                
                if (!$dominio) {
                    Log::warning('Dominio no encontrado', [
                        'dominio_original' => $dominioNombre,
                        'dominio_limpio' => $dominioNombreLimpio,
                        'dominio_sin_www' => $dominioSinWww ?? null,
                    ]);
                    
                    return response()->json([
                        'error' => 'No se encontró cliente para este dominio',
                        'dominio_buscado' => $dominioNombre
                    ], 404);
                }
            }

            // Verificar si el dominio tiene un cliente asociado
            if (!$dominio->cliente) {
                Log::warning('Dominio sin cliente asociado', [
                    'dominio' => $dominioNombre,
                    'dominio_id' => $dominio->id,
                ]);
                
                return response()->json([
                    'error' => 'No se encontró cliente para este dominio'
                ], 404);
            }

            $cliente = $dominio->cliente;

            // Preparar respuesta en el formato especificado
            $response = [
                'idCliente' => (string) $cliente->id, // ID del cliente en este sistema
                'nombre' => $cliente->name ?? '',
                'email' => $cliente->email ?? null,
                'telefono' => $cliente->phone ?? null,
                'nif' => $cliente->cif ?? null,
                'direccion' => $cliente->address ?? null,
                'codigoPostal' => $cliente->zipcode ?? null,
                'ciudad' => $cliente->city ?? null,
                'provincia' => $cliente->province ?? null,
                'pais' => $cliente->country ?? 'España',
            ];

            // Agregar identificador_externo si existe
            if (!empty($cliente->identificador_externo)) {
                $response['idCliente'] = $cliente->identificador_externo;
            }

            Log::info('=== FIN obtenerClientePorDominio ===', [
                'dominio' => $dominioNombre,
                'cliente_id' => $cliente->id,
                'nombre' => $cliente->name,
            ]);

            return response()->json($response, 200);

        } catch (\Exception $e) {
            Log::error('Error al obtener cliente por dominio', [
                'dominio' => $dominioNombre,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Error al procesar la solicitud: ' . $e->getMessage()
            ], 500);
        }
    }
}
