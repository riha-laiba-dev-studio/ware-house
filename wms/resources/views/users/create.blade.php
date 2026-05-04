@extends('layouts.app')
@section('title','Add User')
@section('page-title','Add New User')

@section('content')
<div class="max-w-xl mx-auto">
<form method="POST" action="{{ route('users.store') }}">
@csrf
<div class="card">
  <div class="card-header"><h3 class="font-semibold text-gray-700">User Information</h3></div>
  <div class="card-body space-y-4">
    <div><label class="form-label">Full Name *</label><input type="text" name="name" value="{{ old('name') }}" class="form-input" required></div>
    <div><label class="form-label">Email Address *</label><input type="email" name="email" value="{{ old('email') }}" class="form-input" required></div>
    <div><label class="form-label">Phone</label><input type="text" name="phone" value="{{ old('phone') }}" class="form-input"></div>
    <div><label class="form-label">Role *</label>
      <select name="role" class="form-select" required>
        <option value="">Select Role</option>
        @foreach($roles as $r)<option value="{{ $r->name }}" {{ old('role')==$r->name?'selected':'' }}>{{ $r->name }}</option>@endforeach
      </select>
    </div>
    <div><label class="form-label">Password *</label><input type="password" name="password" class="form-input" required minlength="8"></div>
    <div><label class="form-label">Confirm Password *</label><input type="password" name="password_confirmation" class="form-input" required></div>
  </div>
</div>
<div class="flex gap-3 mt-4">
  <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Create User</button>
  <a href="{{ route('users.index') }}" class="btn-outline">Cancel</a>
</div>
</form>
</div>
@endsection
