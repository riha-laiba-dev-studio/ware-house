@extends('layouts.app')
@section('title','Add Item')
@section('page-title','Add New Product')

@section('content')
<form method="POST" action="{{ route('items.store') }}" enctype="multipart/form-data">
@csrf
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
  <div class="lg:col-span-2 space-y-4">
    <div class="card">
      <div class="card-header"><h3 class="font-semibold text-gray-700">Basic Information</h3></div>
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
        <div>
          <label class="form-label">Category *</label>
          <select name="category_id" class="form-select" required>
            <option value="">Select Category</option>
            @foreach($categories as $c)<option value="{{ $c->id }}" {{ old('category_id')==$c->id?'selected':'' }}>{{ $c->name }}</option>@endforeach
          </select>
        </div>
        <div>
          <label class="form-label">Unit *</label>
          <select name="unit_id" class="form-select" required>
            <option value="">Select Unit</option>
            @foreach($units as $u)<option value="{{ $u->id }}" {{ old('unit_id')==$u->id?'selected':'' }}>{{ $u->name }} ({{ $u->symbol }})</option>@endforeach
          </select>
        </div>
        <div>
          <label class="form-label">Brand</label>
          <select name="brand_id" class="form-select">
            <option value="">Select Brand</option>
            @foreach($brands as $b)<option value="{{ $b->id }}" {{ old('brand_id')==$b->id?'selected':'' }}>{{ $b->name }}</option>@endforeach
          </select>
        </div>
        <div>
          <label class="form-label">Alert Quantity</label>
          <input type="number" name="alert_quantity" value="{{ old('alert_quantity',10) }}" class="form-input" min="0">
        </div>
        <div class="md:col-span-2">
          <label class="form-label">Description</label>
          <textarea name="description" rows="2" class="form-input">{{ old('description') }}</textarea>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><h3 class="font-semibold text-gray-700">Pricing</h3></div>
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
      <div class="card-header"><h3 class="font-semibold text-gray-700">Product Image</h3></div>
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

@push('scripts')
<script>
document.getElementById('imageInput').addEventListener('change', function(){
  const file = this.files[0];
  if(file){ const reader = new FileReader(); reader.onload = e => { document.getElementById('imagePreview').innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`; }; reader.readAsDataURL(file); }
});
</script>
@endpush
@endsection
