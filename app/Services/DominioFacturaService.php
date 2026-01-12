<?php

namespace App\Services;

use App\Models\Dominios\Dominio;
use App\Models\Invoices\Invoice;
use App\Models\Invoices\InvoiceReferenceAutoincrement;
use App\Models\PaymentMethods\PaymentMethod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DominioFacturaService
{
    /**
     * Crear factura desde un evento de Stripe
     */
    public function crearFacturaDesdeStripe(Dominio $dominio, $stripeInvoice)
    {
        DB::beginTransaction();
        
        try {
            // Verificar si ya existe factura para esta suscripción/año
            $añoFacturacion = Carbon::parse($stripeInvoice->created)->year;
            $fechaPago = Carbon::parse($stripeInvoice->created)->format('Y-m-d');
            
            $facturaExistente = Invoice::where('client_id', $dominio->client_id)
                ->where('concept', 'LIKE', "%Renovación dominio {$dominio->dominio}%")
                ->whereYear('created_at', $añoFacturacion)
                ->where('paid_date', $fechaPago)
                ->first();

            if ($facturaExistente) {
                Log::info('Factura ya existe para este dominio y año', [
                    'dominio_id' => $dominio->id,
                    'factura_id' => $facturaExistente->id,
                    'año' => $añoFacturacion,
                    'stripe_invoice_id' => $stripeInvoice->id
                ]);
                
                // Actualizar dominio si no tiene factura_id
                if (!$dominio->factura_id) {
                    $dominio->update(['factura_id' => $facturaExistente->id]);
                }
                
                DB::commit();
                return $facturaExistente;
            }

            // Generar referencia de factura
            $referencia = $this->generarReferenciaFactura();

            // Calcular importes
            $importes = $this->calcularImportes($stripeInvoice->amount_paid / 100); // Convertir de céntimos a euros

            // Obtener método de pago Stripe
            $paymentMethod = PaymentMethod::where(function($query) {
                    $query->where('name', 'LIKE', '%Stripe%')
                          ->orWhere('name', 'LIKE', '%Tarjeta%')
                          ->orWhere('name', 'LIKE', '%Tarjeta de crédito%')
                          ->orWhere('name', 'LIKE', '%Credit Card%');
                })
                ->first();

            // Si no existe, crear uno o usar el primero disponible
            if (!$paymentMethod) {
                $paymentMethod = PaymentMethod::first();
                if (!$paymentMethod) {
                    Log::warning('No se encontró método de pago, usando null', [
                        'dominio_id' => $dominio->id
                    ]);
                }
            }

            // Crear factura
            $factura = Invoice::create([
                'budget_id' => null, // No viene de presupuesto
                'reference' => $referencia['reference'],
                'reference_autoincrement_id' => $referencia['id'],
                'admin_user_id' => null, // Sistema automático
                'client_id' => $dominio->client_id,
                'project_id' => null,
                'payment_method_id' => $paymentMethod->id ?? null,
                'invoice_status_id' => 3, // Cobrada (ya pagada por Stripe)
                'concept' => "Renovación dominio {$dominio->dominio}",
                'description' => "Renovación anual del dominio {$dominio->dominio}. Factura generada automáticamente desde Stripe.",
                'gross' => $importes['total'],
                'base' => $importes['base'],
                'iva' => $importes['iva'],
                'iva_percentage' => 21,
                'discount' => 0,
                'discount_percentage' => 0,
                'total' => $importes['total'],
                'paid_date' => Carbon::parse($stripeInvoice->created)->format('Y-m-d'),
                'paid_amount' => $importes['total'],
                'created_at' => Carbon::parse($stripeInvoice->created),
            ]);

            // Actualizar dominio con factura
            $dominio->update([
                'factura_id' => $factura->id
            ]);

            DB::commit();

            Log::info('Factura creada desde Stripe', [
                'dominio_id' => $dominio->id,
                'factura_id' => $factura->id,
                'stripe_invoice_id' => $stripeInvoice->id,
                'total' => $importes['total']
            ]);

            return $factura;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear factura desde Stripe', [
                'dominio_id' => $dominio->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Generar referencia de factura
     */
    private function generarReferenciaFactura()
    {
        $now = Carbon::now();
        $year = $now->format('Y');
        $monthNum = $now->format('m');

        // Mapeo de mes a letra
        $monthLetter = '';
        switch ($monthNum) {
            case '01': $monthLetter = 'A'; break;
            case '02': $monthLetter = 'B'; break;
            case '03': $monthLetter = 'C'; break;
            case '04': $monthLetter = 'D'; break;
            case '05': $monthLetter = 'E'; break;
            case '06': $monthLetter = 'F'; break;
            case '07': $monthLetter = 'G'; break;
            case '08': $monthLetter = 'H'; break;
            case '09': $monthLetter = 'I'; break;
            case '10': $monthLetter = 'J'; break;
            case '11': $monthLetter = 'K'; break;
            case '12': $monthLetter = 'L'; break;
        }

        // Buscar última referencia con bloqueo para evitar duplicados
        $latestReference = InvoiceReferenceAutoincrement::where('year', $year)
            ->whereNull('ceuta')
            ->where('month_num', $monthNum)
            ->lockForUpdate()
            ->orderBy('id', 'desc')
            ->first();

        $newReferenceAutoincrement = $latestReference 
            ? $latestReference->reference_autoincrement + 1 
            : 1;

        $formattedAutoIncrement = str_pad($newReferenceAutoincrement, 4, '0', STR_PAD_LEFT);
        $reference = $monthLetter . $year . '-' . $formattedAutoIncrement;

        // Verificar que no exista
        $existingInvoice = Invoice::where('reference', $reference)->first();
        if ($existingInvoice) {
            throw new \Exception("Ya existe una factura con la referencia: {$reference}");
        }

        // Guardar referencia
        $referenceToSave = InvoiceReferenceAutoincrement::create([
            'reference_autoincrement' => $newReferenceAutoincrement,
            'year' => $year,
            'month_num' => $monthNum,
            'letter_months' => $monthLetter,
        ]);

        return [
            'id' => $referenceToSave->id,
            'reference' => $reference,
            'reference_autoincrement' => $newReferenceAutoincrement,
        ];
    }

    /**
     * Calcular importes (base, IVA, total)
     * Asume que el precio_venta ya incluye IVA
     */
    private function calcularImportes($precioVenta)
    {
        // Si precio_venta ya incluye IVA (21%)
        $base = round($precioVenta / 1.21, 2);
        $iva = round($precioVenta - $base, 2);
        $total = $precioVenta;

        return [
            'base' => $base,
            'iva' => $iva,
            'total' => $total
        ];
    }
}
