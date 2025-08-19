<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoices\Invoice;
use App\Models\Invoices\InvoiceConcepts;
use App\Models\Budgets\Budget;
use App\Models\Budgets\BudgetConcept;

class UpdateExistingInvoiceDiscounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:update-discount-percentages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza el campo discount_percentage en todas las facturas existentes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando actualización de porcentajes de descuento en facturas existentes...');

        // Obtener todas las facturas
        $invoices = Invoice::all();
        $totalInvoices = $invoices->count();
        $updatedInvoices = 0;
        $updatedConcepts = 0;

        $this->info("Total de facturas encontradas: {$totalInvoices}");

        $progressBar = $this->output->createProgressBar($totalInvoices);
        $progressBar->start();

        foreach ($invoices as $invoice) {
            try {
                // Obtener el presupuesto asociado
                $budget = $invoice->budget;
                
                if ($budget) {
                    // Obtener todos los conceptos de la factura
                    $invoiceConcepts = InvoiceConcepts::where('invoice_id', $invoice->id)->get();
                    
                    foreach ($invoiceConcepts as $invoiceConcept) {
                        // Buscar el concepto original del presupuesto
                        $budgetConcept = BudgetConcept::where('budget_id', $budget->id)
                            ->where('title', $invoiceConcept->title)
                            ->where('concept', $invoiceConcept->concept)
                            ->first();
                        
                        if ($budgetConcept && $budgetConcept->discount !== null) {
                            // Calcular el porcentaje de descuento basándose en el importe y el total sin descuento
                            if ($invoiceConcept->total_no_discount > 0) {
                                $calculatedPercentage = ($invoiceConcept->discount / $invoiceConcept->total_no_discount) * 100;
                                
                                // Actualizar el concepto de la factura
                                $invoiceConcept->update([
                                    'discount_percentage' => round($calculatedPercentage, 2)
                                ]);
                                
                                $updatedConcepts++;
                            }
                        }
                    }
                    
                    $updatedInvoices++;
                }
            } catch (\Exception $e) {
                $this->error("Error procesando factura ID {$invoice->id}: " . $e->getMessage());
            }
            
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("✅ Actualización completada:");
        $this->info("   - Facturas procesadas: {$updatedInvoices}");
        $this->info("   - Conceptos actualizados: {$updatedConcepts}");
        $this->info("   - Campo discount_percentage actualizado en todas las facturas existentes");

        return Command::SUCCESS;
    }
}
