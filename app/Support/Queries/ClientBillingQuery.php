<?php

namespace App\Support\Queries;

use App\Models\Clients\Client;
use Carbon\Carbon;
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
}

