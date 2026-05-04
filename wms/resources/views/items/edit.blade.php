@extends('layouts.app')
@section('title','Edit Item')
@section('page-title','Edit Product')

@section('content')
<form method="POST" action="{{ route('items.update',$item) }}" enctype="multipart/form-data">
@csrf @method('PUT')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
  <div class="lg:col-span-2 space-y-4">
    <div class="card">
      <div class="card-header"><h3 class="font-semibold text-gray-700">Basic Information</h3></div>
      <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2"><label class="form-label">Item Name *</label><input type="text" name="name" value="{{ old('name',$item->name) }}" class="form-input" required></div>
        <div><label class="form-label">SKU</label><input type="text" name="sku" value="{{ old('sku',$item->sku) }}" class="form-input"></div>
        <div><label class="form-label">Barcode</label><input type="text" name="barcode" value="{{ old('barcode',$item->barcode) }}" class="form-input"></div>
        <div><label class="form-label">Category *</label>
          <select name="category_id" class="form-select" required>
            @foreach($categories as $c)<option value="{{ $c->id }}" {{ (old('category_id',$item->category_id)==$c->id)?'selected':'' }}>{{ $c->name }}</option>@endforeach
          </select>
        </div>
        <div><label class="form-label">Unit *</label>
          <select name="unit_id" class="form-select" required>
            @foreach($units as $u)<option value="{{ $u->id }}" {{ (old('unit_id',$item->unit_id)==$u->id)?'selected':'' }}>{{ $u->name }}</option>@endforeach
          </select>
        </div>
        <div><label class="form-label">Brand</label>
          <select name="brand_id" class="form-select">
            <option value="">No Brand</option>
            @foreach($brands as $b)<option value="{{ $b->id }}" {{ (old('brand_id',$item->brand_id)==$b->id)?'selected':'' }}>{{ $b->name }}</option>@endforeach
          </select>
        </div>
        <div><label class="form-label">Alert Quantity</label><input type="number" name="alert_quantity" value="{{ old('alert_quantity',$item->alert_quantity) }}" class="form-input" min="0"></div>
        <div class="flex items-center gap-3 md:col-span-2">
          <input type="checkbox" name="is_active" value="1" id="isActive" {{ $item->is_active?'checked':'' }} class="rounded text-blue-600">
          <label for="isActive" class="text-sm font-medium">Active</label>
        </div>
        <div class="md:col-span-2"><label class="form-label">Description</label><textarea name="description" rows="2" class="form-input">{{ old('description',$item->description) }}</textarea></div>
      </div>
    </div>
    <div class="card">
      <div class="card-header"><h3 class="font-semibold text-gray-700">Pricing</h3></div>
      <div class="card-body grid grid-cols-3 gap-4">
        <div><label class="form-label">Purchase Price *</label><input type="number" name="purchase_price" value="{{ old('purchase_price',$item->purchase_price) }}" class="form-input" min="0" step="0.01" required></div>
        <div><label class="form-label">Selling Price *</label><input type="number" name="selling_price" value="{{ old('selling_price',$item->selling_price) }}" class="form-input" min="0" step="0.01" required></div>
        <div><label class="form-label">Min Selling Price</label><input type="number" name="min_selling_price" value="{{ old('min_selling_price',$item->min_selling_price) }}" class="form-input" min="0" step="0.01"></div>
      </div>
    </div>
  </div>
  <div class="space-y-4">
    <div class="card">
      <div class="card-header"><h3 class="font-semibold text-gray-700">Product Image</h3></div>
      <div class="card-body text-center">
        <div id="imagePreview" class="w-full h-36 bg-gray-100 rounded-lg flex items-center justify-center mb-3 overflow-hidden">
          @if($item->image)<img src="{{ $item->image_url }}" class="w-full h-full object-cover">
          @else<i class="fas fa-image text-gray-300 text-4xl"></i>@endif
        </div>
        <label class="btn-outline cursor-pointer"><i class="fas fa-upload"></i> Change Image<input type="file" name="image" class="hidden" accept="image/*" id="imageInput"></label>
      </div>
    </div>
    <button type="submit" class="btn-primary w-full justify-center"><i class="fas fa-save"></i> Update Item</button>
    <a href="{{ route('items.show',$item) }}" class="btn-outline w-full justify-center">Cancel</a>
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
