<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Clients\Client;
use App\Models\Contacts\Contact;
use App\Models\Services\Service;
use App\Models\Services\ServiceCategories;
use App\Models\Budgets\Budget;
use App\Models\Invoices\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AIController extends Controller
{
    /**
     * Buscar cliente por nombre, CIF, email o teléfono
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function buscarCliente(Request $request)
    {
        try {
            $query = $request->input('q');
            
            if (!$query) {
                return response()->json([
                    'ok' => false,
                    'data' => null,
                    'mensaje' => 'Parámetro de búsqueda "q" es requerido'
                ], 400);
            }

            $clientes = Client::where('is_client', 1)
                ->where(function($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('company', 'like', "%{$query}%")
                      ->orWhere('email', 'like', "%{$query}%")
                      ->orWhere('cif', 'like', "%{$query}%")
                      ->orWhere('phone', 'like', "%{$query}%");
                })
                ->select([
                    'id', 'name', 'primerApellido', 'segundoApellido', 
                    'company', 'email', 'cif', 'phone', 'city', 'province'
                ])
                ->limit(20)
                ->get();

            return response()->json([
                'ok' => true,
                'data' => $clientes,
                'mensaje' => 'Búsqueda realizada correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'data' => null,
                'mensaje' => 'Error al buscar cliente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener resumen del cliente con contexto
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getClienteResumen(Request $request)
    {
        try {
            $clienteId = $request->input('cliente_id');
            
            if (!$clienteId) {
                return response()->json([
                    'ok' => false,
                    'data' => null,
                    'mensaje' => 'Parámetro "cliente_id" es requerido'
                ], 400);
            }

            $cliente = Client::with(['gestor', 'presupuestos' => function($query) {
                $query->orderBy('created_at', 'desc')->limit(5);
            }, 'facturas' => function($query) {
                $query->orderBy('created_at', 'desc')->limit(5);
            }])
            ->find($clienteId);

            if (!$cliente) {
                return response()->json([
                    'ok' => false,
                    'data' => null,
                    'mensaje' => 'Cliente no encontrado'
                ], 404);
            }

            // Calcular estadísticas
            $totalPresupuestos = $cliente->presupuestos()->count();
            $totalFacturas = $cliente->facturas()->count();
            $facturasPagadas = $cliente->facturas()->where('invoice_status_id', 2)->count(); // Asumiendo que 2 es pagado
            
            // Obtener último presupuesto y factura
            $ultimoPresupuesto = $cliente->presupuestos()->latest()->first();
            $ultimaFactura = $cliente->facturas()->latest()->first();

            $resumen = [
                'id' => $cliente->id,
                'nombre' => $cliente->name . ' ' . $cliente->primerApellido . ' ' . $cliente->segundoApellido,
                'empresa' => $cliente->company,
                'email' => $cliente->email,
                'cif' => $cliente->cif,
                'telefono' => $cliente->phone,
                'ciudad' => $cliente->city,
                'provincia' => $cliente->province,
                'gestor' => $cliente->gestor ? $cliente->gestor->name : null,
                'tipo_cliente' => $cliente->tipoCliente,
                'estadisticas' => [
                    'total_presupuestos' => $totalPresupuestos,
                    'total_facturas' => $totalFacturas,
                    'facturas_pagadas' => $facturasPagadas,
                    'tasa_pago' => $totalFacturas > 0 ? round(($facturasPagadas / $totalFacturas) * 100, 2) : 0
                ],
                'ultimo_presupuesto' => $ultimoPresupuesto ? [
                    'id' => $ultimoPresupuesto->id,
                    'total' => $ultimoPresupuesto->total,
                    'fecha' => $ultimoPresupuesto->created_at->format('d/m/Y'),
                    'estado' => $ultimoPresupuesto->budget_status_id
                ] : null,
                'ultima_factura' => $ultimaFactura ? [
                    'id' => $ultimaFactura->id,
                    'total' => $ultimaFactura->total,
                    'fecha' => $ultimaFactura->created_at->format('d/m/Y'),
                    'estado' => $ultimaFactura->invoice_status_id
                ] : null
            ];

            return response()->json([
                'ok' => true,
                'data' => $resumen,
                'mensaje' => 'Resumen del cliente obtenido correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'data' => null,
                'mensaje' => 'Error al obtener resumen del cliente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener precio, plazos y disponibilidad de un producto/servicio
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductoPrecio(Request $request)
    {
        try {
            $productoId = $request->input('producto_id');
            
            if (!$productoId) {
                return response()->json([
                    'ok' => false,
                    'data' => null,
                    'mensaje' => 'Parámetro "producto_id" es requerido'
                ], 400);
            }

            $producto = Service::with('servicoNombre')->find($productoId);

            if (!$producto) {
                return response()->json([
                    'ok' => false,
                    'data' => null,
                    'mensaje' => 'Producto/servicio no encontrado'
                ], 404);
            }

            // Calcular precio medio si está disponible
            $precioMedio = null;
            if (method_exists($producto, 'calcularPrecioMedio')) {
                // Obtener precio por hora de la empresa (asumiendo que existe)
                $precioPorHora = 50; // Valor por defecto, debería obtenerse de configuración
                $precioMedio = $producto->calcularPrecioMedio($precioPorHora);
            }

            $infoProducto = [
                'id' => $producto->id,
                'titulo' => $producto->title,
                'concepto' => $producto->concept,
                'precio_base' => $producto->price,
                'precio_medio' => $precioMedio,
                'categoria' => $producto->servicoNombre ? $producto->servicoNombre->name : null,
                'disponible' => $producto->inactive == 0,
                'estado' => $producto->estado,
                'terminos' => $producto->servicoNombre ? $producto->servicoNombre->terms : null,
                'tipo' => $producto->servicoNombre ? $producto->servicoNombre->type : null
            ];

            return response()->json([
                'ok' => true,
                'data' => $infoProducto,
                'mensaje' => 'Información del producto obtenida correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'data' => null,
                'mensaje' => 'Error al obtener información del producto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener contactos asociados a un cliente
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getClientesContactos(Request $request)
    {
        try {
            $clienteId = $request->input('cliente_id');
            
            if (!$clienteId) {
                return response()->json([
                    'ok' => false,
                    'data' => null,
                    'mensaje' => 'Parámetro "cliente_id" es requerido'
                ], 400);
            }

            $cliente = Client::find($clienteId);

            if (!$cliente) {
                return response()->json([
                    'ok' => false,
                    'data' => null,
                    'mensaje' => 'Cliente no encontrado'
                ], 404);
            }

            $contactos = $cliente->contacto()->select([
                'id', 'name', 'primerApellido', 'segundoApellido', 
                'email', 'phone', 'position', 'notes'
            ])->get();

            return response()->json([
                'ok' => true,
                'data' => $contactos,
                'mensaje' => 'Contactos del cliente obtenidos correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'data' => null,
                'mensaje' => 'Error al obtener contactos del cliente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buscar productos/servicios
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function buscarProducto(Request $request)
    {
        try {
            $query = $request->input('q');
            
            if (!$query) {
                return response()->json([
                    'ok' => false,
                    'data' => null,
                    'mensaje' => 'Parámetro de búsqueda "q" es requerido'
                ], 400);
            }

            $productos = Service::with('servicoNombre')
                ->where('inactive', 0)
                ->where(function($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('concept', 'like', "%{$query}%");
                })
                ->select([
                    'id', 'services_categories_id', 'title', 'concept', 
                    'price', 'estado', 'inactive'
                ])
                ->limit(20)
                ->get();

            // Formatear respuesta
            $productosFormateados = $productos->map(function($producto) {
                return [
                    'id' => $producto->id,
                    'titulo' => $producto->title,
                    'concepto' => $producto->concept,
                    'precio' => $producto->price,
                    'categoria' => $producto->servicoNombre ? $producto->servicoNombre->name : null,
                    'disponible' => $producto->inactive == 0
                ];
            });

            return response()->json([
                'ok' => true,
                'data' => $productosFormateados,
                'mensaje' => 'Búsqueda de productos realizada correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'data' => null,
                'mensaje' => 'Error al buscar productos: ' . $e->getMessage()
            ], 500);
        }
    }
}
