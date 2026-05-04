@extends('layouts.app')
@section('title','Expenses')
@section('page-title','Manage Expenses')

@section('content')
<div class="flex items-center justify-between mb-5">
  <form class="flex gap-2">
    <input type="date" name="from" value="{{ request('from') }}" class="form-input w-36">
    <input type="date" name="to"   value="{{ request('to') }}"   class="form-input w-36">
    <button class="btn-primary btn-sm">Filter</button>
    <a href="{{ route('expenses.index') }}" class="btn-outline btn-sm">Reset</a>
  </form>
  <a href="{{ route('expenses.create') }}" class="btn-primary"><i class="fas fa-plus"></i> Add Expense</a>
</div>

<div class="grid grid-cols-3 gap-3 mb-4">
  <div class="card p-4"><p class="text-xs text-gray-500">Total Expenses</p><p class="text-xl font-bold text-red-600">PKR {{ number_format($total,2) }}</p></div>
  <div class="card p-4"><p class="text-xs text-gray-500">This Month</p><p class="text-xl font-bold text-gray-800">PKR {{ number_format(\App\Models\Expense::whereBetween('expense_date',[now()->startOfMonth(),now()->endOfMonth()])->sum('amount'),2) }}</p></div>
  <div class="card p-4"><p class="text-xs text-gray-500">Categories</p><p class="text-xl font-bold text-gray-800">{{ $categories->count() }}</p></div>
</div>

<div class="card">
  <div class="overflow-x-auto">
    <table class="table">
      <thead><tr><th>Reference</th><th>Category</th><th>Warehouse</th><th>Date</th><th>Amount</th><th>Method</th><th>Notes</th><th>Action</th></tr></thead>
      <tbody>
        @forelse($expenses as $e)
        <tr>
          <td class="font-mono text-xs text-blue-600">{{ $e->reference }}</td>
          <td>{{ $e->category->name }}</td>
          <td>{{ $e->warehouse?->name ?? 'General' }}</td>
          <td>{{ $e->expense_date->format('d M Y') }}</td>
          <td class="font-bold text-red-600">PKR {{ number_format($e->amount,2) }}</td>
          <td class="capitalize">{{ $e->payment_method }}</td>
          <td class="text-gray-400 text-xs">{{ Str::limit($e->notes,40) }}</td>
          <td>
            <form method="POST" action="{{ route('expenses.destroy',$e) }}">@csrf @method('DELETE')
              <button data-confirm="Delete expense?" class="btn btn-sm text-red-600 border border-red-200 hover:bg-red-50 px-2"><i class="fas fa-trash"></i></button>
            </form>
          </td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center py-8 text-gray-400">No expenses found</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="px-4 py-3 border-t">{{ $expenses->links() }}</div>
</div>
@endsection
