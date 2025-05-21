<?php

namespace App\Models\Accounting;

use App\Models\Invoices\Invoice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UnclassifiedIncome extends Model
{
    use HasFactory;

    protected $table = 'unclassified_income';

    protected $fillable = [
        'pdf_file_name',
        'company_name',
        'bank',
        'iban',
        'amount',
        'received_date',
        'invoice_number',
        'order_number',
        'accepted',
        'message',
        'documents',
        'status',
        'relacion',
        'parcial'
    ];

    protected $casts = [
        'relacion' => 'array',
    ];

    public function ingresoRelacion()
    {
        return $this->belongsTo(Ingreso::class);
    }

    public function obtenerRelacion() {
        $relacion = $this->relacion;
        if($relacion){
            $tabla = $relacion['tabla'];
            $id = $relacion['id'];
            if($tabla == 1){
                return Invoice::find($id);
            }
            if($tabla == 2){
                return Ingreso::find($id);
            }
            if($tabla == 3){
                return Gasto::find($id);
            }
            if($tabla == 4){
                return AssociatedExpenses::find($id);
            }
        }
        return null;
    }

}