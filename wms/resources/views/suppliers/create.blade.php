@extends('layouts.app')
@section('title','Add Supplier')
@section('page-title','Add Supplier / Vendor')

@section('content')
<div class="max-w-2xl mx-auto">
<form method="POST" action="{{ route('suppliers.store') }}">
@csrf
<div class="card">
  <div class="card-header"><h3 class="font-semibold">Supplier Information</h3></div>
  <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-4">
    <div><label class="form-label">Full Name *</label><input type="text" name="name" value="{{ old('name') }}" class="form-input" required></div>
    <div><label class="form-label">Supplier Code *</label><input type="text" name="code" value="{{ old('code') }}" class="form-input" required placeholder="e.g. VN-001"></div>
    <div><label class="form-label">Email</label><input type="email" name="email" value="{{ old('email') }}" class="form-input"></div>
    <div><label class="form-label">Phone</label><input type="text" name="phone" value="{{ old('phone') }}" class="form-input"></div>
    <div><label class="form-label">Company</label><input type="text" name="company" value="{{ old('company') }}" class="form-input"></div>
    <div><label class="form-label">City</label><input type="text" name="city" value="{{ old('city') }}" class="form-input"></div>
    <div><label class="form-label">Country</label><input type="text" name="country" value="{{ old('country','Pakistan') }}" class="form-input"></div>
    <div><label class="form-label">Opening Balance (PKR)</label><input type="number" name="opening_balance" value="{{ old('opening_balance',0) }}" class="form-input" min="0" step="0.01"></div>
    <div class="md:col-span-2"><label class="form-label">Address</label><textarea name="address" rows="2" class="form-input">{{ old('address') }}</textarea></div>
    <div class="md:col-span-2"><label class="form-label">Notes</label><textarea name="notes" rows="2" class="form-input">{{ old('notes') }}</textarea></div>
  </div>
</div>
<div class="flex gap-3 mt-4">
  <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Save Supplier</button>
  <a href="{{ route('suppliers.index') }}" class="btn-outline">Cancel</a>
</div>
</form>
</div>
@endsection
