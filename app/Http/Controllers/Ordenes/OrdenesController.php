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

    public function actualizar2()
    {
        // Rango de fechas especificado
        $startDate = '2024-01-01';
        $endDate = '2024-01-31';

        // Obtener los registros filtrados por la columna `received_date`
        $expenses = AssociatedExpenses::whereBetween('received_date', [$startDate, $endDate])->get();

        // Preparar el array para el resultado final
        $result = $expenses->map(function ($expense) {
            // Calcular IVA: (quantity * 21) / 121
            $iva_cantidad = ($expense->quantity * 21) / 121;

            // Calcular total sin IVA: quantity - iva_cantidad
            $total_sin_iva = $expense->quantity - $iva_cantidad;

            // Retornar el registro con las nuevas columnas
            return [
                'id' => $expense->id,
                'title' => $expense->title,
                'reference' => $expense->reference,
                'quantity' => $expense->quantity,
                'received_date' => $expense->received_date,
                'iva_cantidad' => $iva_cantidad,
                'total_sin_iva' => $total_sin_iva,
            ];
        });

        // Retornar el array completo
        return $result->toArray();
    }

    public function actualizar()
{
    // Filtrar registros del año 2024 completo
    $startDate = '2024-01-01';
    $endDate = '2024-12-31';

    // Obtener los registros filtrados por la columna `received_date`
    $expenses = AssociatedExpenses::whereBetween('received_date', [$startDate, $endDate])->get();

    // Preparar el array para el resultado final
    $result = $expenses->map(function ($expense) {
        // Verificar si la columna `iva` es null o 0.00
        if (is_null($expense->iva) || $expense->iva == 0.00) {
            // Calcular IVA: (quantity * 21) / 121
            $iva_cantidad = round(($expense->quantity * 21) / 121, 2);

            // Calcular total sin IVA: quantity - iva_cantidad
            $total_sin_iva = round($expense->quantity - $iva_cantidad, 2);
        } else {
            // Si ya tiene IVA, usar los valores originales
            $iva_cantidad = round($expense->iva, 2);
            $total_sin_iva = round($expense->quantity - $iva_cantidad, 2);
        }

        // Validar que la suma de total_sin_iva + iva_cantidad sea igual a quantity
        $quantity = round($total_sin_iva + $iva_cantidad, 2);

        // Retornar el registro con las nuevas columnas
        return [
            'id' => $expense->id,
            'title' => $expense->title,
            'reference' => $expense->reference,
            'quantity' => $expense->quantity,
            'received_date' => $expense->received_date,
            'iva' => $expense->iva,
            'iva_cantidad' => $iva_cantidad,
            'total_sin_iva' => $total_sin_iva,
            'validated_quantity' => $quantity, // Validación para debug (opcional)
        ];
    });

    // Retornar el array completo
    return $result->toArray();
}


}
