<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::all();
        return view('units.index', compact('units'));
    }

    public function create()
    {
        return view('units.create');
    }

    public function storeUnit(Request $request)
    {
        $unit = Unit::create([
            'name' => $request->name,
            'symbol' => $request->symbol,
            'is_active' => 1
        ]);

        return response()->json($unit);
    }
}
