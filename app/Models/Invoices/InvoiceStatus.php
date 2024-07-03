<?php

namespace App\Models\Invoices;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceStatus extends Model
{
    use HasFactory;
    
    protected $table = 'invoice_status';
    public $timestamps = false;

    /**
     * Atributos asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

      /**
     * ESTADO: Abierta
     *
     * @var string
     */
    const INVOICE_STATUS_PENDING = "Abierta";
    /**
     * ESTADO: No cobrada
     *
     * @var string
     */
    const INVOICE_STATUS_NOT_PAID = "No cobrada";
    
    /**
     * ESTADO: Cobrada
     *
     * @var string
     */
    const INVOICE_STATUS_PAID = "Cobrada";
    
    /**
     * ESTADO: Cobrada parcialmente
     *
     * @var string
     */
    const INVOICE_STATUS_PAID_PARTIALLY = "Cobrada parcialmente";
}
