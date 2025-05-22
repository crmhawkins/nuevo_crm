<?php

namespace App\Console\Commands;

use App\Models\Accounting\AssociatedExpenses;
use App\Models\Alerts\Alert;
use App\Models\Users\User;
use Illuminate\Console\Command;
use Carbon\Carbon;

class OrdenesAlerta extends Command
{
    protected $signature = 'Ordenes:Alerta';
    protected $description = 'alerta de ordenes a pagar';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $now = Carbon::now();
        $cutoffDate = $now->day <= 10
            ? $now->subMonthNoOverflow()->startOfMonth()->addDays(22) // Día 23 del mes anterior
            : $now->startOfMonth()->addDays(22); // Día 23 del mes actual

        $ordenes = AssociatedExpenses::where('state', 'PENDIENTE')
            ->where('aceptado_gestor',1)
            ->whereDate('date_aceptado', '<', $cutoffDate) // Fecha antes del 23
            ->join('purchase_order', 'associated_expenses.purchase_order_id', '=', 'purchase_order.id')
            ->join('budget_concepts', 'purchase_order.budget_concept_id', '=', 'budget_concepts.id') // Join para llegar a los conceptos
            ->join('budgets', 'budget_concepts.budget_id', '=', 'budgets.id') // Join para llegar a los presupuestos
            ->join('admin_user', 'budgets.admin_user_id', '=', 'admin_user.id') // Join para llegar al usuario
            ->join('clients', 'purchase_order.client_id', '=', 'clients.id')
            ->join('suppliers', 'purchase_order.supplier_id', '=', 'suppliers.id')
            ->select('associated_expenses.*', 'clients.name as clienteNombre','suppliers.name as proveedorNombre', 'admin_user.name as gestorNombre','purchase_order.id as orden','budgets.id as presupuesto')->get();

            $usuarios = User::where('access_level_id', 3)->get();
            foreach ($usuarios as $usuario) {
                $alert = Alert::create([
                    'admin_user_id' => $usuario->id,
                    'stage_id' => 43,
                    'status_id' => 1,
                    'activation_datetime' => Carbon::now(),
                    'description' => 'Tienes ' . count($ordenes).' ordenes pendientes de pago',
                ]);
            }
            $this->info('¡Comando ejecutado exitosamente!');
    }

}
