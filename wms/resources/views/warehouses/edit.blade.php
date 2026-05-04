@extends('layouts.app')
@section('title','Edit Warehouse')
@section('page-title','Edit Warehouse')

@section('content')
<div class="max-w-2xl mx-auto">
<form method="POST" action="{{ route('warehouses.update',$warehouse) }}">
@csrf @method('PUT')
<div class="card">
  <div class="card-header"><h3 class="font-semibold">Edit: {{ $warehouse->name }}</h3></div>
  <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-4">
    <div><label class="form-label">Name *</label><input type="text" name="name" value="{{ old('name',$warehouse->name) }}" class="form-input" required></div>
    <div><label class="form-label">Code *</label><input type="text" name="code" value="{{ old('code',$warehouse->code) }}" class="form-input" required></div>
    <div><label class="form-label">City</label><input type="text" name="city" value="{{ old('city',$warehouse->city) }}" class="form-input"></div>
    <div><label class="form-label">Phone</label><input type="text" name="phone" value="{{ old('phone',$warehouse->phone) }}" class="form-input"></div>
    <div><label class="form-label">Email</label><input type="email" name="email" value="{{ old('email',$warehouse->email) }}" class="form-input"></div>
    <div><label class="form-label">Manager</label>
      <select name="manager_id" class="form-select">
        <option value="">Select Manager</option>
        @foreach($managers as $m)<option value="{{ $m->id }}" {{ old('manager_id',$warehouse->manager_id)==$m->id?'selected':'' }}>{{ $m->name }}</option>@endforeach
      </select>
    </div>
    <div class="flex items-center gap-3">
      <input type="checkbox" name="is_active" value="1" id="isActive" {{ $warehouse->is_active?'checked':'' }} class="rounded text-blue-600">
      <label for="isActive" class="text-sm font-medium text-gray-700">Active</label>
    </div>
    <div class="md:col-span-2"><label class="form-label">Address</label><textarea name="address" rows="2" class="form-input">{{ old('address',$warehouse->address) }}</textarea></div>
    <div class="md:col-span-2"><label class="form-label">Notes</label><textarea name="notes" rows="2" class="form-input">{{ old('notes',$warehouse->notes) }}</textarea></div>
  </div>
</div>
<div class="flex gap-3 mt-4">
  <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Update Warehouse</button>
  <a href="{{ route('warehouses.index') }}" class="btn-outline">Cancel</a>
</div>
</form>
</div>
@endsection
