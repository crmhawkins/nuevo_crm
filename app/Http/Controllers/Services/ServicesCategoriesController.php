<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\Services\ServiceCategories;
use Illuminate\Http\Request;

class ServicesCategoriesController extends Controller
{
    public function index()
    {
        $servicios = ServiceCategories::paginate(2);
        return view('services-categories.index', compact('servicios'));
    }
}
