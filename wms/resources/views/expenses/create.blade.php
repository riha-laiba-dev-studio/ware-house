@extends('layouts.app')
@section('title','Add Expense')
@section('page-title','Record Expense')

@section('content')
<div class="max-w-xl mx-auto">
<form method="POST" action="{{ route('expenses.store') }}">
@csrf

<div class="card">
  <div class="card-header">
    <h3 class="font-semibold text-gray-700">Expense Details</h3>
  </div>

  <div class="card-body space-y-4">

    {{-- CATEGORY WITH ADD BUTTON --}}
    <div>
      <label class="form-label">Category *</label>
      <div class="flex gap-2">
        <select name="expense_category_id" id="categorySelect" class="form-select" required>
          <option value="">Select Category</option>
          @foreach($categories as $c)
            <option value="{{ $c->id }}">{{ $c->name }}</option>
          @endforeach
        </select>

        <button type="button" onclick="openCategoryModal()" class="btn-outline px-3">
          +
        </button>
      </div>
    </div>

    <div>
      <label class="form-label">Warehouse</label>
      <select name="warehouse_id" class="form-select">
        <option value="">General (All Warehouses)</option>
        @foreach($warehouses as $w)
          <option value="{{ $w->id }}">{{ $w->name }}</option>
        @endforeach
      </select>
    </div>

    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="form-label">Amount (PKR) *</label>
        <input type="number" name="amount" class="form-input" min="0.01" step="0.01" required>
      </div>
      <div>
        <label class="form-label">Date *</label>
        <input type="date" name="expense_date" value="{{ date('Y-m-d') }}" class="form-input" required>
      </div>
    </div>

    <div>
      <label class="form-label">Payment Method *</label>
      <select name="payment_method" class="form-select" required>
        <option value="cash">Cash</option>
        <option value="bank">Bank Transfer</option>
        <option value="cheque">Cheque</option>
      </select>
    </div>

    <div>
      <label class="form-label">Notes</label>
      <textarea name="notes" rows="2" class="form-input"></textarea>
    </div>

  </div>
</div>

<div class="flex gap-3 mt-4">
  <button type="submit" class="btn-primary">
    <i class="fas fa-save"></i> Save Expense
  </button>
  <a href="{{ route('expenses.index') }}" class="btn-outline">Cancel</a>
</div>

</form>
</div>

{{-- 🔥 CATEGORY MODAL --}}
<div id="categoryModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-white p-6 rounded-xl shadow-xl w-80">
    <h3 class="font-semibold text-gray-700 mb-3">Add Category</h3>

    <input type="text" id="newCategoryName" class="form-input w-full" placeholder="Enter category name">

    <div class="flex justify-end gap-2 mt-4">
      <button onclick="closeCategoryModal()" class="btn-outline">Cancel</button>
      <button onclick="saveCategory()" class="btn-primary">Save</button>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
function openCategoryModal() {
  $('#categoryModal').removeClass('hidden');
}

function closeCategoryModal() {
  $('#categoryModal').addClass('hidden');
}

function saveCategory() {
  let name = $('#newCategoryName').val();

  if (!name) {
    alert('Enter category name');
    return;
  }

  $.ajax({
  url: '/expense-categories',
  method: 'POST',
  data: {
    name: name,
    _token: $('meta[name="csrf-token"]').attr('content')
  },
  success: function(res) {
    $('#categorySelect').append(
      `<option value="${res.id}" selected>${res.name}</option>`
    );

    closeCategoryModal();
    $('#newCategoryName').val('');
  },
  error: function() {
    alert('Error adding category');
  }
});
}
</script>
@endpush