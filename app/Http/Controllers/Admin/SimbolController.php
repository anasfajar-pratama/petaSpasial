<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\SymbolCatalog;

class SimbolController extends Controller
{
    public function index()
    {
        $catalog = SymbolCatalog::read();

        $markers = collect($catalog['markers'])->sortBy('category')->values();
        $lines = collect($catalog['lines'])->sortBy('name')->values();
        $fills = collect($catalog['fills'])->sortBy('name')->values();

        return view('admin.simbol.index', compact('markers', 'lines', 'fills'));
    }
}