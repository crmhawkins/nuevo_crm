<?php

namespace App\Support\Queries;

use App\Models\Clients\Client;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class ClientBillingQuery
{
    /**
     * Construye la consulta base para obtener clientes por facturación.
     */
    public static function build(Carbon $fechaInicio, Carbon $fechaFin, ?string $buscarCliente = null): Builder
    {
        $query = Client::select([
                'clients.id as client_id',
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
            ->whereBetween('invoices.created_at', [$fechaInicio, $fechaFin])
            ->whereIn('invoices.invoice_status_id', [3, 4])
            ->whereNull('invoices.deleted_at');

        if ($buscarCliente) {
            $query->where(function (Builder $q) use ($buscarCliente) {
                $q->where('clients.name', 'like', "%{$buscarCliente}%")
                    ->orWhere('clients.primerApellido', 'like', "%{$buscarCliente}%")
                    ->orWhere('clients.segundoApellido', 'like', "%{$buscarCliente}%")
                    ->orWhere('clients.company', 'like', "%{$buscarCliente}%")
                    ->orWhere('clients.phone', 'like', "%{$buscarCliente}%");
            });
        }

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
     * Aplica filtros de facturación mínima y máxima.
     */
    public static function applyBillingRange(Builder $query, ?float $min, ?float $max): Builder
    {
        if ($min !== null) {
            $query->having('total_facturado', '>=', $min);
        }

        if ($max !== null) {
            $query->having('total_facturado', '<=', $max);
        }

        return $query;
    }

    /**
     * Devuelve un paginador manual sobre la consulta agregada.
     */
    public static function paginate(
        Carbon $fechaInicio,
        Carbon $fechaFin,
        ?string $buscarCliente,
        int $page,
        int $perPage,
        ?float $minFacturacion = null,
        ?float $maxFacturacion = null,
        string $sort = 'billing_desc',
        ?string $path = null,
        array $queryParams = []
    ): LengthAwarePaginator {
        $baseQuery = self::build($fechaInicio, $fechaFin, $buscarCliente);
        self::applyBillingRange($baseQuery, $minFacturacion, $maxFacturacion);

        $subQuery = DB::query()->fromSub($baseQuery, 'billing');

        $orderedQuery = match ($sort) {
            'billing_asc' => $subQuery->orderBy('billing.total_facturado')->orderBy('billing.name'),
            'name' => $subQuery->orderBy('billing.name'),
            'oldest' => $subQuery->orderBy('billing.created_at'),
            'recent' => $subQuery->orderByDesc('billing.created_at'),
            default => $subQuery->orderByDesc('billing.total_facturado')->orderBy('billing.name'),
        };

        $paginator = $orderedQuery->paginate($perPage, ['*'], 'page', $page);

        if (!empty($queryParams)) {
            $paginator->appends($queryParams);
        }

        return $paginator;
    }
}

