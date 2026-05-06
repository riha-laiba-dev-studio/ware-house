<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();
        if ($request->search) $query->where(fn($q) => $q->where('name','like','%'.$request->search.'%')->orWhere('phone','like','%'.$request->search.'%'));
        $customers = $query->paginate(15)->withQueryString();
        return view('customers.index', compact('customers'));
    }
    public function create() { return view('customers.create'); }
    public function store(Request $request)
    {
        $data = $request->validate(['name'=>'required|string|max:255','code'=>'required|string|unique:customers','email'=>'nullable|email','phone'=>'nullable|string|max:20','company'=>'nullable|string','address'=>'nullable|string','city'=>'nullable|string','country'=>'nullable|string','opening_balance'=>'nullable|numeric|min:0','credit_limit'=>'nullable|numeric|min:0','notes'=>'nullable|string']);
        Customer::create($data);
        return redirect()->route('customers.index')->with('success','Customer created.');
    }
    public function show(Customer $customer) {
        $customer->load(['sales.warehouse']);
        return view('customers.show', compact('customer'));
    }
    public function edit(Customer $customer) { return view('customers.edit', compact('customer')); }
    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate(['name'=>'required|string|max:255','code'=>'required|string|unique:customers,code,'.$customer->id,'email'=>'nullable|email','phone'=>'nullable|string|max:20','company'=>'nullable|string','address'=>'nullable|string','city'=>'nullable|string','country'=>'nullable|string','opening_balance'=>'nullable|numeric|min:0','credit_limit'=>'nullable|numeric|min:0','is_active'=>'boolean','notes'=>'nullable|string']);
        $customer->update($data);
        return redirect()->route('customers.index')->with('success','Customer updated.');
    }
    public function destroy(Customer $customer) { $customer->delete(); return back()->with('success','Customer deleted.'); }
}
