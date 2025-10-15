<?php

namespace App\Http\Controllers\Autoseo;

use App\Http\Controllers\Controller;
use App\Models\Autoseo\Autoseo;
use App\Jobs\ProcessCompanyContextJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Storage;

class AutoseoController extends Controller
{
    public function index()
    {

        $clients = Autoseo::all();
        return view('autoseo.index', compact('clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_email' => 'required|email|max:255',
            'url' => 'required|url|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'user_app' => 'required|string|max:255',
            'password_app' => 'required|string|max:255',
            'CompanyName' => 'nullable|string|max:255',
            'AddressLine1' => 'nullable|string|max:255',
            'Locality' => 'nullable|string|max:255',
            'AdminDistrict' => 'nullable|string|max:255',
            'PostalCode' => 'nullable|string|max:20',
            'CountryRegion' => 'nullable|string|size:2',
            'company_context' => 'required|string|min:100|max:2000',
        ]);

        $client = new Autoseo();
        $client->fill($validated);
        $client->pin = bin2hex(random_bytes(4)); // Genera un PIN aleatorio de 8 caracteres
        $client->save();

        // Procesar el contexto empresarial con IA en segundo plano si existe
        if (!empty($validated['company_context'])) {
            Log::info("📤 Despachando Job para procesar contexto del cliente ID: {$client->id}");
            ProcessCompanyContextJob::dispatch($client->id, $validated['company_context']);
        }

        return redirect()->route('autoseo.index')->with('success', 'Cliente creado correctamente');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:autoseo,id',
            'client_name' => 'required|string|max:255',
            'client_email' => 'required|email|max:255',
            'url' => 'required|url|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'user_app' => 'required|string|max:255',
            'password_app' => 'required|string|max:255',
            'CompanyName' => 'nullable|string|max:255',
            'AddressLine1' => 'nullable|string|max:255',
            'Locality' => 'nullable|string|max:255',
            'AdminDistrict' => 'nullable|string|max:255',
            'PostalCode' => 'nullable|string|max:20',
            'CountryRegion' => 'nullable|string|size:2',
            'company_context' => 'required|string|min:100|max:2000',
        ]);

        $client = Autoseo::findOrFail($request->id);
        
        // Guardar el contexto original para comparar
        $originalContext = $client->company_context;
        
        $client->fill($validated);
        $client->save();

        // Procesar el contexto empresarial con IA en segundo plano si existe y ha cambiado
        if (!empty($validated['company_context']) && $validated['company_context'] !== $originalContext) {
            Log::info("📤 Despachando Job para actualizar contexto del cliente ID: {$client->id}");
            ProcessCompanyContextJob::dispatch($client->id, $validated['company_context']);
        }

        return redirect()->route('autoseo.index')->with('success', 'Cliente actualizado correctamente');
    }

    public function delete(Request $request)
    {
        $client = Autoseo::find($request->id);
        $client->delete();
        return redirect()->route('autoseo.index')->with('success', 'Cliente eliminado correctamente');
    }
}
