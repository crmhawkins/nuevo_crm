<?php

namespace App\Http\Controllers\Autoseo;

use App\Http\Controllers\Controller;
use App\Models\Autoseo\Autoseo;
use App\Models\Autoseo\SeoProgramacion;
use App\Jobs\ProcessCompanyContextJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Storage;

class AutoseoController extends Controller
{
    public function index()
    {
        $clients = Autoseo::with(['programaciones' => function($query) {
            $query->where('estado', 'pendiente')
                  ->orderBy('fecha_programada');
        }])->get();

        // Calcular alertas para cada cliente
        $clients->each(function($client) {
            $nextProgramacion = $client->programaciones->first();
            if ($nextProgramacion) {
                $nextDate = Carbon::parse($nextProgramacion->fecha_programada);
                $daysUntil = Carbon::now()->diffInDays($nextDate, false);
                
                // Calcular meses restantes de programaciones
                $totalProgramaciones = $client->programaciones->count();
                $lastProgramacion = $client->programaciones->last();
                $lastDate = Carbon::parse($lastProgramacion->fecha_programada);
                $monthsUntilExpiration = Carbon::now()->diffInMonths($lastDate, false);
                
                $client->alert_info = [
                    'next_date' => $nextDate,
                    'days_until' => $daysUntil,
                    'is_expiring_soon' => $daysUntil <= 7 && $daysUntil >= 0,
                    'is_overdue' => $daysUntil < 0,
                    'total_pending' => $totalProgramaciones,
                    'months_until_expiration' => $monthsUntilExpiration,
                    'is_expiring_in_one_month' => $monthsUntilExpiration <= 1 && $monthsUntilExpiration >= 0,
                ];
            } else {
                $client->alert_info = [
                    'next_date' => null,
                    'days_until' => null,
                    'is_expiring_soon' => false,
                    'is_overdue' => false,
                    'total_pending' => 0,
                    'months_until_expiration' => null,
                    'is_expiring_in_one_month' => false,
                ];
            }
        });

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
            // Campos de configuración periódica
            'seo_frequency' => 'nullable|in:manual,weekly,biweekly,monthly,bimonthly,quarterly',
            'seo_day_of_month' => 'nullable|in:1,5,10,15,20,25,last',
            'seo_day_of_week' => 'nullable|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'seo_time' => 'nullable|date_format:H:i',
            'auto_email_reports' => 'nullable|boolean',
            'auto_advanced_reports' => 'nullable|boolean',
        ]);

        $client = new Autoseo();
        $client->fill($validated);
        $client->pin = bin2hex(random_bytes(4)); // Genera un PIN aleatorio de 8 caracteres
        $client->save();

        // Crear programaciones periódicas si no es manual
        if ($validated['seo_frequency'] && $validated['seo_frequency'] !== 'manual') {
            $this->createSeoSchedule($client, $validated);
        }

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
            // Campos de configuración periódica
            'seo_frequency' => 'nullable|in:manual,weekly,biweekly,monthly,bimonthly,quarterly',
            'seo_day_of_month' => 'nullable|in:1,5,10,15,20,25,last',
            'seo_day_of_week' => 'nullable|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'seo_time' => 'nullable|date_format:H:i',
        ]);

        $client = Autoseo::findOrFail($request->id);
        
        // Guardar el contexto original para comparar
        $originalContext = $client->company_context;
        
        $client->fill($validated);
        $client->save();

        // Reprogramar SEO si se cambió la configuración periódica
        if (isset($validated['seo_frequency']) && $validated['seo_frequency'] !== 'manual') {
            // Eliminar programaciones existentes pendientes
            SeoProgramacion::where('autoseo_id', $client->id)
                ->where('estado', 'pendiente')
                ->delete();
            
            // Crear nuevas programaciones
            $this->createSeoSchedule($client, $validated);
        }

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

    /**
     * Crea una programación SEO puntual para hoy
     */
    public function createPuntualSeo(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:autoseo,id',
            'hora' => 'nullable|date_format:H:i',
        ]);

        $client = Autoseo::findOrFail($validated['client_id']);
        $hora = $validated['hora'] ?? '09:00';
        $fechaHoy = Carbon::today()->format('Y-m-d') . ' ' . $hora;

        // Crear la programación puntual para hoy
        SeoProgramacion::create([
            'autoseo_id' => $client->id,
            'fecha_programada' => $fechaHoy,
            'estado' => 'pendiente',
        ]);

        Log::info("⚡ SEO Puntual creado para cliente ID {$client->id} ({$client->client_name}) - Fecha: {$fechaHoy}");

        return redirect()->route('autoseo.index')->with('success', "SEO puntual creado para {$client->client_name} hoy a las {$hora}");
    }

    /**
     * Crea programaciones periódicas de SEO para un cliente
     */
    private function createSeoSchedule($client, $config)
    {
        $frequency = $config['seo_frequency'];
        $time = $config['seo_time'] ?? '09:00';
        
        // Calcular fechas según la frecuencia
        $dates = $this->calculateScheduleDates($frequency, $config);
        
        foreach ($dates as $date) {
            SeoProgramacion::create([
                'autoseo_id' => $client->id,
                'fecha_programada' => $date,
                'estado' => 'pendiente',
            ]);
        }
        
        Log::info("📅 Programaciones SEO creadas para cliente ID {$client->id}: " . count($dates) . " fechas");
    }

    /**
     * Calcula las fechas de programación según la frecuencia configurada
     */
    private function calculateScheduleDates($frequency, $config)
    {
        $dates = [];
        $now = Carbon::now();
        
        switch ($frequency) {
            case 'weekly':
                $dayOfWeek = $config['seo_day_of_week'] ?? 'friday';
                $startDate = $now->next($dayOfWeek);
                
                // Crear programaciones para los próximos 12 meses
                for ($i = 0; $i < 52; $i++) {
                    $dates[] = $startDate->copy()->addWeeks($i)->format('Y-m-d');
                }
                break;
                
            case 'biweekly':
                $dayOfWeek = $config['seo_day_of_week'] ?? 'friday';
                $startDate = $now->next($dayOfWeek);
                
                // Crear programaciones para los próximos 12 meses
                for ($i = 0; $i < 26; $i++) {
                    $dates[] = $startDate->copy()->addWeeks($i * 2)->format('Y-m-d');
                }
                break;
                
            case 'monthly':
                $dayOfMonth = $config['seo_day_of_month'] ?? '15';
                $startDate = $this->getNextMonthlyDate($now, $dayOfMonth);
                
                // Crear programaciones para los próximos 12 meses
                for ($i = 0; $i < 12; $i++) {
                    $dates[] = $startDate->copy()->addMonths($i)->format('Y-m-d');
                }
                break;
                
            case 'bimonthly':
                $dayOfMonth = $config['seo_day_of_month'] ?? '15';
                $startDate = $this->getNextMonthlyDate($now, $dayOfMonth);
                
                // Crear programaciones para los próximos 12 meses
                for ($i = 0; $i < 6; $i++) {
                    $dates[] = $startDate->copy()->addMonths($i * 2)->format('Y-m-d');
                }
                break;
                
            case 'quarterly':
                $dayOfMonth = $config['seo_day_of_month'] ?? '15';
                $startDate = $this->getNextMonthlyDate($now, $dayOfMonth);
                
                // Crear programaciones para los próximos 12 meses
                for ($i = 0; $i < 4; $i++) {
                    $dates[] = $startDate->copy()->addMonths($i * 3)->format('Y-m-d');
                }
                break;
        }
        
        return $dates;
    }

    /**
     * Calcula la próxima fecha mensual según el día especificado
     */
    private function getNextMonthlyDate($now, $dayOfMonth)
    {
        if ($dayOfMonth === 'last') {
            $nextMonth = $now->copy()->addMonth();
            return $nextMonth->endOfMonth();
        }
        
        $day = (int) $dayOfMonth;
        $nextDate = $now->copy()->day($day);
        
        // Si ya pasó este mes, ir al próximo mes
        if ($nextDate->isPast()) {
            $nextDate->addMonth();
        }
        
        // Ajustar si el día no existe en el mes (ej: 31 en febrero)
        if (!$nextDate->isValid()) {
            $nextDate->endOfMonth();
        }
        
        return $nextDate;
    }
}
