<?php

namespace App\Http\Controllers\Dominios;

use App\Http\Controllers\Controller;
use App\Models\Clients\Client;
use App\Models\Dominios\Dominio;
use App\Models\Dominios\estadosDominios;
use App\Models\Invoices\InvoiceConcepts;
use App\Models\Invoices\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DominiosController extends Controller
{
    public function index()
    {
        $dominios = Dominio::paginate(2);
        return view('dominios.index', compact('dominios'));
    }

    public function show($id)
    {
        $dominio = Dominio::with(['cliente', 'estadoName'])->find($id);
        
        if (!$dominio) {
            return redirect()->route('dominios.index')->with('toast', [
                'icon' => 'error',
                'mensaje' => 'Dominio no encontrado'
            ]);
        }

        // Buscar facturas asociadas a este dominio
        $facturasAsociadas = $this->buscarFacturasAsociadas($dominio->dominio, $dominio->client_id);

        return view('dominios.show', compact('dominio', 'facturasAsociadas'));
    }

    public function edit($id)
    {
        $dominio = Dominio::find($id);
        $clientes = Client::all();
        $estados = estadosDominios::all();

        return view('dominios.edit', compact('dominio','clientes','estados'));
    }
    public function create()
    {
        $clientes = Client::all();
        return view('dominios.create', compact('clientes'));
    }

    public function store(Request $request)
    {
        // Validamos los campos
        $this->validate($request, [
            'dominio' => 'required|max:200',
            'client_id' => 'required',
            'date' => 'required',
            'precio_compra' => 'nullable|numeric|min:0',
            'precio_venta' => 'nullable|numeric|min:0',
            'iban' => 'nullable|string|max:100',

        ], [
            'dominio.required' => 'El nombre es requerido para continuar',
            'client_id.required' => 'El cliente es requerido para continuar',
            'date.required' => 'La fecha de contratación es requerido para continuar',
            'precio_compra.numeric' => 'El precio de compra debe ser un número válido',
            'precio_venta.numeric' => 'El precio de venta debe ser un número válido',
            'iban.string' => 'El IBAN debe ser una cadena de texto válida',

        ]);

        $data = $request->all();

        // Crear una instancia de Carbon para la fecha de inicio
        $dateStart = Carbon::parse($request->input('date'));

        // Calcular la fecha de finalización agregando un año
        // $data['data_start'] = $dateStart->format('Y-m-d');
        // Calcular la fecha de finalización agregando un año
        $data['date_end'] = $dateStart->addYear()->format('Y-m-d H:i:s'); // Formato DATETIME

        // Formatear date_start para asegurarse que sea en el formato correcto
        $data['date_start'] = Carbon::parse($request->input('date_start'))->format('Y-m-d H:i:s');
        $data['estado_id'] = 3;

        $crearDominio = Dominio::create($data);


        session()->flash('toast', [
            'icon' => 'success',
            'mensaje' => 'El dominio se creo correctamente'
        ]);

        return redirect()->route('dominios.index');
    }

    public function update(Request $request, $id)
    {

        $dominio = Dominio::find($id);
        // Validamos los campos
        $this->validate($request, [
            'dominio' => 'required|max:200',
            'client_id' => 'required',
            'date' => 'required',
            'precio_compra' => 'nullable|numeric|min:0',
            'precio_venta' => 'nullable|numeric|min:0',
            'iban' => 'nullable|string|max:100',

        ], [
            'dominio.required' => 'El nombre es requerido para continuar',
            'client_id.required' => 'El cliente es requerido para continuar',
            'date.required' => 'La fecha de contratación es requerido para continuar',
            'precio_compra.numeric' => 'El precio de compra debe ser un número válido',
            'precio_venta.numeric' => 'El precio de venta debe ser un número válido',
            'iban.string' => 'El IBAN debe ser una cadena de texto válida',

        ]);

        $data = $request->all();

        // Crear una instancia de Carbon para la fecha de inicio
        $dateStart = Carbon::parse($request->input('date'));
        // dd($dateStart);

        // Calcular la fecha de finalización agregando un año
        // $data['data_start'] = $dateStart->format('Y-m-d');
        // Calcular la fecha de finalización agregando un año
        $data['date_end'] = $dateStart->addYear()->format('Y-m-d H:i:s'); // Formato DATETIME

        // Formatear date_start para asegurarse que sea en el formato correcto
        $data['date_start'] = Carbon::parse($request->input('date_start'))->format('Y-m-d H:i:s');
        $dominio->update(attributes: $data);


        session()->flash('toast', [
            'icon' => 'success',
            'mensaje' => 'El dominio se creo correctamente'
        ]);

        return redirect()->route('dominios.index');
    }

    public function destroy(Request $request)
    {
        $domino = Dominio::find($request->id);

        if (!$domino) {
            return response()->json([
                'error' => true,
                'mensaje' => "Error en el servidor, intentelo mas tarde."
            ]);
        }

        $domino->delete();
        return response()->json([
            'error' => false,
            'mensaje' => 'El usuario fue borrado correctamente'
        ]);
    }

    /**
     * Buscar facturas asociadas a un dominio
     */
    private function buscarFacturasAsociadas($domainName, $clienteId)
    {
        // Normalizar el dominio para búsqueda
        $normalizedDomain = $this->normalizeDomain($domainName);
        
        // Buscar en conceptos de facturas del mismo cliente
        $conceptos = InvoiceConcepts::with(['invoice.budget.cliente', 'invoice.invoiceStatus'])
            ->whereHas('invoice.budget', function($query) use ($clienteId) {
                $query->where('client_id', $clienteId);
            })
            ->where(function($query) use ($normalizedDomain) {
                $query->where('title', 'like', "%{$normalizedDomain}%")
                      ->orWhere('concept', 'like', "%{$normalizedDomain}%");
            })
            ->get();

        $facturas = collect();
        
        foreach ($conceptos as $concepto) {
            if ($concepto->invoice && 
                $concepto->invoice->budget && 
                $concepto->invoice->budget->cliente &&
                $concepto->invoice->budget->client_id == $clienteId &&
                !$facturas->contains('id', $concepto->invoice->id)) {
                $facturas->push($concepto->invoice);
            }
        }

        return $facturas;
    }

    /**
     * Normalizar dominio para búsqueda
     */
    private function normalizeDomain($domain)
    {
        $domain = strtolower(trim($domain));
        $domain = preg_replace('/^https?:\/\//', '', $domain);
        $domain = preg_replace('/^www\./', '', $domain);
        $domain = rtrim($domain, '/'); // Eliminar barra final
        return $domain;
    }

    /**
     * Cambiar estado del dominio a cancelado
     */
    public function cancelar($id)
    {
        try {
            $dominio = Dominio::find($id);
            
            if (!$dominio) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dominio no encontrado'
                ], 404);
            }

            $dominio->update(['estado_id' => 2]); // ID 2 = Cancelado

            return response()->json([
                'success' => true,
                'message' => 'Dominio cancelado exitosamente',
                'estado' => 'Cancelado'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cancelar el dominio: ' . $e->getMessage()
            ], 500);
        }
    }
}
