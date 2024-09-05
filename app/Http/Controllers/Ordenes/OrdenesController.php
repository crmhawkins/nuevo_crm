<?php

namespace App\Http\Controllers\Ordenes;

use App\Http\Controllers\Controller;
use App\Models\PurcharseOrde\PurcharseOrder;
use App\Models\Users\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrdenesController extends Controller
{
    public function index()
    {
        $orders = PurcharseOrder::paginate(2);
        return view('orders.index', compact('orders'));
    }

}
