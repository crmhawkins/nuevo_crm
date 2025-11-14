<?php

namespace App\Services;

use App\Models\Clients\Client;
use App\Models\Services\Service;
use App\Models\Services\ServiceCategories;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ClientAnalyticsService
{
    /**
     * Obtener un paginador de clientes según el tipo de análisis solicitado.
     */
    public function paginateClients(array $filters): LengthAwarePaginator
    {
        $perPageOptions = [10, 15, 25, 50, 100];
        $perPage = (int) ($filters['per_page'] ?? 15);
        if (!in_array($perPage, $perPageOptions, true)) {
            $perPage = 15;
        }

        $page = max((int) ($filters['page'] ?? 1), 1);
        $sort = $filters['sort'] ?? 'billing_desc';
        $tipoAnalisis = $filters['tipo_analisis'] ?? 'top_clientes';
        $buscar = trim((string) ($filters['buscar_cliente'] ?? ''));
        $filtroId = $filters['filtro_id'] ?? null;
        $montoMinimo = $filters['monto_minimo'] ?? null;

        [$fechaInicio, $fechaFin] = $this->resolveDateRange(
            $filters['fecha_inicio'] ?? null,
            $filters['fecha_fin'] ?? null
        );

        $query = match ($tipoAnalisis) {
            'por_categoria' => $this->buildCategoryQuery($fechaInicio, $fechaFin, $buscar, $filtroId),
            'por_servicio' => $this->buildServiceQuery($fechaInicio, $fechaFin, $buscar, $filtroId),
            'por_facturacion' => $this->buildBillingThresholdQuery($fechaInicio, $fechaFin, $buscar, $montoMinimo, $sort),
            default => $this->buildTopClientsQuery($fechaInicio, $fechaFin, $buscar, $sort),
        };

        return $query->paginate($perPage, ['*'], 'page', $page)
            ->appends($this->buildAppendParameters($filters));
    }

    /**
     * Obtener categorías disponibles para los filtros.
     */
    public function getCategories(): Collection
    {
        return ServiceCategories::select('id', 'name')
            ->where('inactive', false)
            ->orderBy('name')
            ->get();
    }

    /**
     * Obtener servicios disponibles para los filtros.
     */
    public function getServices(): Collection
    {
        return Service::select('services.id', 'services.title', 'services_categories.name as categoria')
            ->join('services_categories', 'services.services_categories_id', '=', 'services_categories.id')
            ->join('invoice_concepts', 'services.id', '=', 'invoice_concepts.service_id')
            ->join('invoices', 'invoice_concepts.invoice_id', '=', 'invoices.id')
            ->whereIn('invoices.invoice_status_id', [3, 4])
            ->where('services.inactive', false)
            ->where('services_categories.inactive', false)
            ->groupBy('services.id', 'services.title', 'services_categories.name')
            ->orderBy('services_categories.name')
            ->orderBy('services.title')
            ->get();
    }

    /**
     * Construye el query para top clientes por facturación.
     */
    protected function buildTopClientsQuery(Carbon $desde, Carbon $hasta, string $buscar, string $sort): Builder
    {
        $query = Client::query()
            ->select([
                'clients.id',
                'clients.name',
                'clients.primerApellido',
                'clients.segundoApellido',
                'clients.company',
                'clients.phone',
                'clients.created_at',
            ])
            ->selectRaw('SUM(invoices.total) as total_facturado')
            ->selectRaw('COUNT(invoices.id) as num_facturas')
            ->join('invoices', 'clients.id', '=', 'invoices.client_id')
            ->where('clients.is_client', true)
            ->whereBetween('invoices.created_at', [$desde, $hasta])
            ->whereIn('invoices.invoice_status_id', [3, 4]);

        $this->applySearchFilter($query, $buscar);
        $this->applySort($query, $sort);

        return $query->groupBy(
            'clients.id',
            'clients.name',
            'clients.primerApellido',
            'clients.segundoApellido',
            'clients.company',
            'clients.phone',
            'clients.created_at'
        );
    }

    /**
     * Construye el query filtrado por categorías de servicios.
     */
    protected function buildCategoryQuery(Carbon $desde, Carbon $hasta, string $buscar, $categoriaId): Builder
    {
        $query = Client::query()
            ->select([
                'clients.id',
                'clients.name',
                'clients.primerApellido',
                'clients.segundoApellido',
                'clients.company',
                'clients.phone',
            ])
            ->selectRaw('services_categories.name as categoria_servicio')
            ->selectRaw('SUM(invoice_concepts.total) as total_por_categoria')
            ->selectRaw('COUNT(DISTINCT invoices.id) as facturas_con_categoria')
            ->join('invoices', 'clients.id', '=', 'invoices.client_id')
            ->join('invoice_concepts', 'invoices.id', '=', 'invoice_concepts.invoice_id')
            ->join('services', 'invoice_concepts.service_id', '=', 'services.id')
            ->join('services_categories', 'services.services_categories_id', '=', 'services_categories.id')
            ->where('clients.is_client', true)
            ->whereBetween('invoices.created_at', [$desde, $hasta])
            ->whereIn('invoices.invoice_status_id', [3, 4]);

        if ($categoriaId) {
            $query->where('services_categories.id', $categoriaId);
        }

        $this->applySearchFilter($query, $buscar);

        return $query->groupBy(
            'clients.id',
            'clients.name',
            'clients.primerApellido',
            'clients.segundoApellido',
            'clients.company',
            'clients.phone',
            'services_categories.id',
            'services_categories.name'
        )->orderByDesc('total_por_categoria');
    }

    /**
     * Construye el query filtrado por servicios concretos.
     */
    protected function buildServiceQuery(Carbon $desde, Carbon $hasta, string $buscar, $servicioId): Builder
    {
        $query = Client::query()
            ->select([
                'clients.id',
                'clients.name',
                'clients.primerApellido',
                'clients.segundoApellido',
                'clients.company',
                'clients.phone',
            ])
            ->selectRaw('services.title as servicio')
            ->selectRaw('SUM(invoice_concepts.total) as total_por_servicio')
            ->selectRaw('COUNT(DISTINCT invoices.id) as facturas_con_servicio')
            ->join('invoices', 'clients.id', '=', 'invoices.client_id')
            ->join('invoice_concepts', 'invoices.id', '=', 'invoice_concepts.invoice_id')
            ->join('services', 'invoice_concepts.service_id', '=', 'services.id')
            ->where('clients.is_client', true)
            ->whereBetween('invoices.created_at', [$desde, $hasta])
            ->whereIn('invoices.invoice_status_id', [3, 4]);

        if ($servicioId) {
            $query->where('services.id', $servicioId);
        }

        $this->applySearchFilter($query, $buscar);

        return $query->groupBy(
            'clients.id',
            'clients.name',
            'clients.primerApellido',
            'clients.segundoApellido',
            'clients.company',
            'clients.phone',
            'services.id',
            'services.title'
        )->orderByDesc('total_por_servicio');
    }

    /**
     * Construye el query filtrado por monto mínimo de facturación.
     */
    protected function buildBillingThresholdQuery(Carbon $desde, Carbon $hasta, string $buscar, $montoMinimo, string $sort): Builder
    {
        $query = $this->buildTopClientsQuery($desde, $hasta, $buscar, $sort);

        if ($montoMinimo !== null && $montoMinimo !== '') {
            $query->having('total_facturado', '>=', (float) $montoMinimo);
        }

        return $query;
    }

    /**
     * Aplica el filtro de búsqueda por nombre, empresa o teléfono.
     */
    protected function applySearchFilter(Builder $query, string $buscar): void
    {
        if ($buscar === '') {
            return;
        }

        $like = '%' . $buscar . '%';
        $query->where(function (Builder $q) use ($like) {
            $q->where('clients.name', 'like', $like)
                ->orWhere('clients.primerApellido', 'like', $like)
                ->orWhere('clients.segundoApellido', 'like', $like)
                ->orWhere('clients.company', 'like', $like)
                ->orWhere('clients.phone', 'like', $like);
        });
    }

    /**
     * Aplica el orden seleccionado a la consulta principal.
     */
    protected function applySort(Builder $query, string $sort): void
    {
        switch ($sort) {
            case 'billing_asc':
                $query->orderBy('total_facturado')->orderBy('clients.name');
                break;
            case 'name':
                $query->orderBy('clients.name');
                break;
            case 'oldest':
                $query->orderBy('clients.created_at');
                break;
            case 'recent':
                $query->orderByDesc('clients.created_at');
                break;
            case 'billing_desc':
            default:
                $query->orderByDesc('total_facturado')->orderBy('clients.name');
                break;
        }
    }

    /**
     * Normaliza el rango de fechas utilizando la zona horaria de la app.
     *
     * @return array{Carbon, Carbon}
     */
    protected function resolveDateRange(?string $fechaInicio, ?string $fechaFin): array
    {
        $timezone = config('app.timezone');
        $inicio = $fechaInicio
            ? Carbon::parse($fechaInicio, $timezone)->startOfDay()
            : Carbon::now($timezone)->subMonth()->startOfMonth();
        $fin = $fechaFin
            ? Carbon::parse($fechaFin, $timezone)->endOfDay()
            : Carbon::now($timezone)->endOfDay();

        if ($inicio->gt($fin)) {
            [$inicio, $fin] = [$fin->copy()->startOfDay(), $inicio->copy()->endOfDay()];
        }

        return [$inicio, $fin];
    }

    /**
     * Mantiene los parámetros relevantes en la paginación.
     */
    protected function buildAppendParameters(array $filters): array
    {
        return collect($filters)
            ->only([
                'fecha_inicio',
                'fecha_fin',
                'tipo_analisis',
                'filtro_id',
                'monto_minimo',
                'buscar_cliente',
                'sort',
            ])
            ->reject(fn ($value) => $value === null || $value === '')
            ->toArray();
    }
}

