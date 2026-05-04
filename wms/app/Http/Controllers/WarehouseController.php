<?php
namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::with('manager')->withCount('inventory')->paginate(15);
        return view('warehouses.index', compact('warehouses'));
    }

    public function create()
    {
        $managers = User::active()->get();
        return view('warehouses.create', compact('managers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'code'       => 'required|string|max:50|unique:warehouses',
            'address'    => 'nullable|string',
            'city'       => 'nullable|string|max:100',
            'phone'      => 'nullable|string|max:20',
            'email'      => 'nullable|email|max:255',
            'manager_id' => 'nullable|exists:users,id',
            'notes'      => 'nullable|string',
        ]);
        Warehouse::create($data);
        return redirect()->route('warehouses.index')->with('success','Warehouse created successfully.');
    }

    public function edit(Warehouse $warehouse)
    {
        $managers = User::active()->get();
        return view('warehouses.edit', compact('warehouse','managers'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'code'       => 'required|string|max:50|unique:warehouses,code,'.$warehouse->id,
            'address'    => 'nullable|string',
            'city'       => 'nullable|string|max:100',
            'phone'      => 'nullable|string|max:20',
            'email'      => 'nullable|email|max:255',
            'manager_id' => 'nullable|exists:users,id',
            'is_active'  => 'boolean',
            'notes'      => 'nullable|string',
        ]);
        $warehouse->update($data);
        return redirect()->route('warehouses.index')->with('success','Warehouse updated successfully.');
    }

    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();
        return redirect()->route('warehouses.index')->with('success','Warehouse deleted.');
    }
}
