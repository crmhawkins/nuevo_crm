<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Budgets\Budget;
use App\Models\Budgets\BudgetConcept;
use App\Models\Budgets\BudgetConceptType;
use App\Models\Budgets\BudgetReferenceAutoincrement;
use App\Models\Invoices\Invoice;
use App\Models\Invoices\InvoiceConcepts;
use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\PortalPurchaseDetail;
use App\Models\Projects\Project;
use App\Models\Services\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class PortalProductos extends Controller
{

    public function productos() {
        $cliente = session('cliente');
        if (!$cliente) {
            return view('portal.login');
        }

        return view('portal.productos', compact('cliente'));
    }
}
