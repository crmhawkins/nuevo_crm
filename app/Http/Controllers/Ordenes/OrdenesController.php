<?php

namespace App\Http\Controllers\Ordenes;

use App\Http\Controllers\Controller;
use App\Models\Accounting\AssociatedExpenses;
use App\Models\PurcharseOrde\PurcharseOrder;
use App\Models\Users\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OrdenesController extends Controller
{
    public function index()
    {
        $user_level = Auth::user()->access_level_id;
        if ($user_level == 3) {
            return view('orders.indexContable');
        }
        return view('orders.index');
    }

    public function indexAll(){
        return view('orders.indexAll');

    }

    public function actualizar()
    {
        // Rango de fechas especificado
        $startDate = '2024-01-01';
        $endDate = '2024-01-31';

        // Obtener los registros filtrados por la columna `received_date`
        $expenses = AssociatedExpenses::where('date', '>=', $startDate)
        ->whereNull('iva')
        ->get();

        // Preparar el array para el resultado final
        $result = $expenses->map(function ($expense) {
            // Calcular IVA: (quantity * 21) / 121
            $iva_cantidad = ($expense->quantity * 21) / 121;

            // Calcular total sin IVA: quantity - iva_cantidad
            $total_sin_iva = $expense->quantity - $iva_cantidad;
            $total = number_format(($expense->quantity * $expense->iva / 100) + $expense->quantity, 2, '.', '');

            $expense->update([
                'iva' => 21.00,
                'quantity' => $total_sin_iva,
                'total' => $total,
            ]);
            // Retornar el registro con las nuevas columnas
            return [
                'id' => $expense->id,
                'title' => $expense->title,
                'reference' => $expense->reference,
                'quantity' => $expense->quantity,
                'received_date' => $expense->received_date,
                'iva_cantidad' => $iva_cantidad,
                'total_sin_iva' => $total_sin_iva,
                'total' => $total,  // Total con IVA

            ];
        });

        // Retornar el array completo
        return $result->toArray();
    }

    public function actualizar3()
    {
        // Filtrar registros del año 2024 completo
        $startDate = '2024-01-01';
        $endDate = '2024-12-31';

        // Obtener los registros filtrados por la columna `received_date`
        $expenses = AssociatedExpenses::whereBetween('received_date', [$startDate, $endDate])
            ->where(function ($query) {
                $query->whereNull('iva')
                    ->orWhere('iva', 0.00);
            })
            ->get();

        // Recorrer y actualizar cada registro
        foreach ($expenses as $expense) {
            // Calcular IVA: (quantity * 21) / 121
            $iva_cantidad = round(($expense->quantity * 21) / 121, 2);

            // Calcular total sin IVA: quantity - iva_cantidad
            $total_sin_iva = round($expense->quantity - $iva_cantidad, 2);

            // Actualizar el registro en la base de datos
            $expense->update([
                'iva' => 21.00,
                'quantity' => $total_sin_iva,
            ]);
        }

        // Retornar mensaje de éxito o registros actualizados (opcional)
        return [
            'message' => 'Gastos actualizados correctamente.',
            'updated_count' => $expenses->count(),
        ];
    }

    public function actualizar2()
{
    // Filtrar registros del año 2024 completo
    $startDate = '2024-01-01';
    $endDate = '2024-12-31';

    // Obtener los registros filtrados por la columna `received_date`
    $expenses = AssociatedExpenses::whereBetween('received_date', [$startDate, $endDate])
        ->get();

    // Recorrer y actualizar cada registro
    foreach ($expenses as $expense) {
        // Si IVA es nulo o 0, aplicar cálculo
        if (is_null($expense->iva) || $expense->iva == 0.00) {
            // Calcular IVA: (quantity * 21) / 121
            $iva_cantidad = number_format(($expense->quantity * 21) / 121, 2, '.', '');

            // Calcular total sin IVA: quantity - iva_cantidad
            $total_sin_iva = number_format($expense->quantity - $iva_cantidad, 2, '.', '');

            // Calcular el total con IVA: (quantity * iva / 100) + quantity
            $total = number_format(($expense->quantity * 21 / 100) + $expense->quantity, 2, '.', '');
        } else {
            // Si ya tiene IVA, usamos el valor de iva y calculamos el total
            $iva_cantidad = number_format($expense->iva, 2, '.', '');
            $total_sin_iva = number_format($expense->quantity - $iva_cantidad, 2, '.', '');

            // Calculamos el total con IVA
            $total = number_format(($expense->quantity * $expense->iva / 100) + $expense->quantity, 2, '.', '');
        }

        // Actualizar el registro en la base de datos
        $expense->update([
            'total' => $total,  // Total con IVA
        ]);
    }

    // Retornar mensaje de éxito o registros actualizados (opcional)
    return [
        'message' => 'Gastos actualizados correctamente.',
        'updated_count' => $expenses->count(),
    ];
}





}
