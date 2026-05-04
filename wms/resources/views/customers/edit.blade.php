@extends('layouts.app')
@section('title','Edit Customer')
@section('page-title','Edit Customer')

@section('content')
<div class="max-w-2xl mx-auto">
<form method="POST" action="{{ route('customers.update',$customer) }}">
@csrf @method('PUT')
<div class="card">
  <div class="card-header"><h3 class="font-semibold">Edit: {{ $customer->name }}</h3></div>
  <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-4">
    <div><label class="form-label">Full Name *</label><input type="text" name="name" value="{{ old('name',$customer->name) }}" class="form-input" required></div>
    <div><label class="form-label">Customer Code *</label><input type="text" name="code" value="{{ old('code',$customer->code) }}" class="form-input" required></div>
    <div><label class="form-label">Email</label><input type="email" name="email" value="{{ old('email',$customer->email) }}" class="form-input"></div>
    <div><label class="form-label">Phone</label><input type="text" name="phone" value="{{ old('phone',$customer->phone) }}" class="form-input"></div>
    <div><label class="form-label">Company</label><input type="text" name="company" value="{{ old('company',$customer->company) }}" class="form-input"></div>
    <div><label class="form-label">City</label><input type="text" name="city" value="{{ old('city',$customer->city) }}" class="form-input"></div>
    <div><label class="form-label">Opening Balance</label><input type="number" name="opening_balance" value="{{ old('opening_balance',$customer->opening_balance) }}" class="form-input" min="0" step="0.01"></div>
    <div><label class="form-label">Credit Limit</label><input type="number" name="credit_limit" value="{{ old('credit_limit',$customer->credit_limit) }}" class="form-input" min="0" step="0.01"></div>
    <div class="flex items-center gap-3">
      <input type="checkbox" name="is_active" value="1" id="isActive" {{ $customer->is_active ? 'checked' : '' }} class="rounded text-blue-600">
      <label for="isActive" class="text-sm font-medium text-gray-700">Active</label>
    </div>
    <div class="md:col-span-2"><label class="form-label">Address</label><textarea name="address" rows="2" class="form-input">{{ old('address',$customer->address) }}</textarea></div>
    <div class="md:col-span-2"><label class="form-label">Notes</label><textarea name="notes" rows="2" class="form-input">{{ old('notes',$customer->notes) }}</textarea></div>
  </div>
</div>
<div class="flex gap-3 mt-4">
  <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Update Customer</button>
  <a href="{{ route('customers.index') }}" class="btn-outline">Cancel</a>
</div>
</form>
</div>
@endsection
