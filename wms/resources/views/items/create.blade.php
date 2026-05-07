@extends('layouts.app')
@section('title','Add Item')
@section('page-title','Add New Product')

@section('content')
<form method="POST" action="{{ route('items.store') }}" enctype="multipart/form-data">
  @csrf
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="lg:col-span-2 space-y-4">
      <div class="card">
        <div class="card-header">
          <h3 class="font-semibold text-gray-700">Basic Information</h3>
        </div>
        <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="md:col-span-2">
            <label class="form-label">Item Name *</label>
            <input type="text" name="name" value="{{ old('name') }}" class="form-input" required>
          </div>
          <div>
            <label class="form-label">SKU (auto-generated)</label>
            <input type="text" name="sku" value="{{ old('sku') }}" class="form-input" placeholder="Leave blank to auto-generate">
          </div>
          <div>
            <label class="form-label">Barcode</label>
            <input type="text" name="barcode" value="{{ old('barcode') }}" class="form-input">
          </div>
          <div class="md:col-span-2">
            {{-- CATEGORY --}}
            <div>
              <label class="form-label">Category *</label>

              <div class="flex gap-2">
                <select name="category_id" id="categorySelect" class="form-select" required>
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

            {{-- UNIT --}}
            <div>
              <label class="form-label">Unit *</label>

              <div class="flex gap-2">
                <select name="unit_id" id="unitSelect" class="form-select" required>
                  <option value="">Select Unit</option>
                  @foreach($units as $u)
                  <option value="{{ $u->id }}">
                    {{ $u->name }} ({{ $u->symbol }})
                  </option>
                  @endforeach
                </select>

                <button type="button" onclick="openUnitModal()" class="btn-outline px-3">
                  +
                </button>
              </div>
            </div>

            {{-- BRAND --}}
            <div>
              <label class="form-label">Brand</label>

              <div class="flex gap-2">
                <select name="brand_id" id="brandSelect" class="form-select">
                  <option value="">Select Brand</option>

                  @foreach($brands as $b)
                  <option value="{{ $b->id }}">{{ $b->name }}</option>
                  @endforeach
                </select>

                <button type="button" onclick="openBrandModal()" class="btn-outline px-3">
                  +
                </button>
              </div>
            </div>

            {{-- ALERT --}}
            <div>
              <label class="form-label">Alert Quantity</label>

              <input type="number"
                name="alert_quantity"
                value="{{ old('alert_quantity',10) }}"
                class="form-input"
                min="0">
            </div>
          </div>
          <div class="md:col-span-2">
            <label class="form-label">Description</label>
            <textarea name="description" rows="2" class="form-input">{{ old('description') }}</textarea>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h3 class="font-semibold text-gray-700">Pricing</h3>
        </div>
        <div class="card-body grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="form-label">Purchase Price (PKR) *</label>
            <input type="number" name="purchase_price" value="{{ old('purchase_price',0) }}" class="form-input" min="0" step="0.01" required>
          </div>
          <div>
            <label class="form-label">Selling Price (PKR) *</label>
            <input type="number" name="selling_price" value="{{ old('selling_price',0) }}" class="form-input" min="0" step="0.01" required>
          </div>
          <div>
            <label class="form-label">Min Selling Price</label>
            <input type="number" name="min_selling_price" value="{{ old('min_selling_price',0) }}" class="form-input" min="0" step="0.01">
          </div>
        </div>
      </div>
    </div>

    <div class="space-y-4">
      <div class="card">
        <div class="card-header">
          <h3 class="font-semibold text-gray-700">Product Image</h3>
        </div>
        <div class="card-body text-center">
          <div id="imagePreview" class="w-full h-36 bg-gray-100 rounded-lg flex items-center justify-center mb-3 overflow-hidden">
            <i class="fas fa-image text-gray-300 text-4xl"></i>
          </div>
          <label class="btn-outline cursor-pointer">
            <i class="fas fa-upload"></i> Upload Image
            <input type="file" name="image" class="hidden" accept="image/*" id="imageInput">
          </label>
        </div>
      </div>
      <div class="flex gap-3 flex-col">
        <button type="submit" class="btn-primary justify-center"><i class="fas fa-save"></i> Save Item</button>
        <a href="{{ route('items.index') }}" class="btn-outline justify-center">Cancel</a>
      </div>
    </div>
  </div>
</form>
{{-- CATEGORY MODAL --}}
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
{{-- brand MODAL --}}

<div id="brandModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-white p-6 rounded-xl shadow-xl w-80">
    <h3 class="font-semibold text-gray-700 mb-3">Add Brand</h3>

    <input type="text" id="newBrandName" class="form-input w-full" placeholder="Brand name">

    <div class="flex justify-end gap-2 mt-4">
      <button onclick="closeBrandModal()" class="btn-outline">Cancel</button>
      <button onclick="saveBrand()" class="btn-primary">Save</button>
    </div>
  </div>
</div>
{{--unit MODAL --}}
<div id="unitModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-white p-6 rounded-xl shadow-xl w-80">
    <h3 class="font-semibold text-gray-700 mb-3">Add Unit</h3>

    <input type="text" id="newUnitName" class="form-input w-full mb-2" placeholder="Unit name">
    <input type="text" id="newUnitSymbol" class="form-input w-full" placeholder="Symbol (kg, pcs, box)">

    <div class="flex justify-end gap-2 mt-4">
      <button onclick="closeUnitModal()" class="btn-outline">Cancel</button>
      <button onclick="saveUnit()" class="btn-primary">Save</button>
    </div>
  </div>
</div>
@push('scripts')
<script>
  document.getElementById('imageInput').addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = e => {
        document.getElementById('imagePreview').innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
      };
      reader.readAsDataURL(file);
    }
  });

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
      url: '/categories',
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
  // brand modal fun
  function openBrandModal() {
    $('#brandModal').removeClass('hidden');
  }

  function closeBrandModal() {
    $('#brandModal').addClass('hidden');
  }

  function saveBrand() {

    $.ajax({
      url: '/brands',
      method: 'POST',
      data: {
        name: $('#newBrandName').val(),
        _token: $('meta[name="csrf-token"]').attr('content')
      },

      success: function(res) {

        $('#brandSelect').append(
          `<option value="${res.id}" selected>
          ${res.name}
        </option>`
        );

        closeBrandModal();

        $('#newBrandName').val('');
      }
    });
  }
  // unit modal fun

  function openUnitModal() {
    $('#unitModal').removeClass('hidden');
  }

  function closeUnitModal() {
    $('#unitModal').addClass('hidden');
  }

  function saveUnit() {

    $.ajax({
      url: '/units',
      method: 'POST',
      data: {
        name: $('#newUnitName').val(),
        symbol: $('#newUnitSymbol').val(),
        _token: $('meta[name="csrf-token"]').attr('content')
      },

      success: function(res) {

        $('#unitSelect').append(
          `<option value="${res.id}" selected>
          ${res.name} (${res.symbol})
        </option>`
        );

        closeUnitModal();

        $('#newUnitName').val('');
        $('#newUnitSymbol').val('');
      }
    });
  }
</script>
@endpush
@endsection