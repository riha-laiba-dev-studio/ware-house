<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with(['category', 'warehouse', 'creator']);
        if ($request->from) $query->whereDate('expense_date', '>=', $request->from);
        if ($request->to)   $query->whereDate('expense_date', '<=', $request->to);
        $expenses = $query->latest()->paginate(15)->withQueryString();
        $total    = $query->sum('amount');
        $categories = ExpenseCategory::active()->get();
        return view('expenses.index', compact('expenses', 'total', 'categories'));
    }
    public function create()
    {
        $categories = ExpenseCategory::active()->get();
        $warehouses = Warehouse::active()->get();
        return view('expenses.create', compact('categories', 'warehouses'));
    }
    public function store(Request $request)
    {
        $data = $request->validate(['expense_category_id' => 'required|exists:expense_categories,id', 'warehouse_id' => 'nullable|exists:warehouses,id', 'amount' => 'required|numeric|min:0.01', 'expense_date' => 'required|date', 'payment_method' => 'required|string', 'notes' => 'nullable|string']);
        $data['reference']  = 'EXP-' . strtoupper(Str::random(8));
        $data['created_by'] = auth()->id();
        Expense::create($data);
        return redirect()->route('expenses.index')->with('success', 'Expense recorded.');
    }
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $category = ExpenseCategory::firstOrCreate(
    ['name' => $request->name],
    ['is_active' => 1]
);

        return response()->json($category);
    }
    public function destroy(Expense $expense)
    {
        $expense->delete();
        return back()->with('success', 'Expense deleted.');
    }
}
