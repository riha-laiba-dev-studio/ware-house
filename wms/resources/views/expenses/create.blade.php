@extends('layouts.app')
@section('title','Add Expense')
@section('page-title','Record Expense')

@section('content')
<div class="max-w-xl mx-auto">
<form method="POST" action="{{ route('expenses.store') }}">
@csrf
<div class="card">
  <div class="card-header"><h3 class="font-semibold text-gray-700">Expense Details</h3></div>
  <div class="card-body space-y-4">
    <div><label class="form-label">Category *</label>
      <select name="expense_category_id" class="form-select" required>
        <option value="">Select Category</option>
        @foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
      </select>
    </div>
    <div><label class="form-label">Warehouse</label>
      <select name="warehouse_id" class="form-select">
        <option value="">General (All Warehouses)</option>
        @foreach($warehouses as $w)<option value="{{ $w->id }}">{{ $w->name }}</option>@endforeach
      </select>
    </div>
    <div class="grid grid-cols-2 gap-4">
      <div><label class="form-label">Amount (PKR) *</label><input type="number" name="amount" class="form-input" min="0.01" step="0.01" required></div>
      <div><label class="form-label">Date *</label><input type="date" name="expense_date" value="{{ date('Y-m-d') }}" class="form-input" required></div>
    </div>
    <div><label class="form-label">Payment Method *</label>
      <select name="payment_method" class="form-select" required>
        <option value="cash">Cash</option><option value="bank">Bank Transfer</option><option value="cheque">Cheque</option>
      </select>
    </div>
    <div><label class="form-label">Notes</label><textarea name="notes" rows="2" class="form-input"></textarea></div>
  </div>
</div>
<div class="flex gap-3 mt-4">
  <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Save Expense</button>
  <a href="{{ route('expenses.index') }}" class="btn-outline">Cancel</a>
</div>
</form>
</div>
@endsection
