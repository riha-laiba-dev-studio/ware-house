<?php
namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();
        if ($request->search) $query->where('name','like','%'.$request->search.'%')->orWhere('phone','like','%'.$request->search.'%');
        $suppliers = $query->withSum(['purchases'=>fn($q)=>$q],'total_amount')->withSum(['purchases'=>fn($q)=>$q],'paid_amount')->paginate(15)->withQueryString();
        return view('suppliers.index', compact('suppliers'));
    }
    public function create() { return view('suppliers.create'); }
    public function store(Request $request)
    {
        $data = $request->validate(['name'=>'required|string|max:255','code'=>'required|string|unique:suppliers','email'=>'nullable|email','phone'=>'nullable|string|max:20','company'=>'nullable|string','address'=>'nullable|string','city'=>'nullable|string','country'=>'nullable|string','opening_balance'=>'nullable|numeric|min:0','notes'=>'nullable|string']);
        Supplier::create($data);
        return redirect()->route('suppliers.index')->with('success','Supplier created.');
    }
    public function show(Supplier $supplier) {
        $supplier->load(['purchases.warehouse']);
        return view('suppliers.show', compact('supplier'));
    }
    public function edit(Supplier $supplier) { return view('suppliers.edit', compact('supplier')); }
    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate(['name'=>'required|string|max:255','code'=>'required|string|unique:suppliers,code,'.$supplier->id,'email'=>'nullable|email','phone'=>'nullable|string|max:20','company'=>'nullable|string','address'=>'nullable|string','city'=>'nullable|string','country'=>'nullable|string','opening_balance'=>'nullable|numeric|min:0','is_active'=>'boolean','notes'=>'nullable|string']);
        $supplier->update($data);
        return redirect()->route('suppliers.index')->with('success','Supplier updated.');
    }
    public function destroy(Supplier $supplier) { $supplier->delete(); return back()->with('success','Supplier deleted.'); }
}
