@extends('layouts.app')
@section('title','Edit User')
@section('page-title','Edit User')

@section('content')
<div class="max-w-xl mx-auto">
<form method="POST" action="{{ route('users.update',$user) }}">
@csrf @method('PUT')
<div class="card">
  <div class="card-header"><h3 class="font-semibold text-gray-700">Edit User — {{ $user->name }}</h3></div>
  <div class="card-body space-y-4">
    <div><label class="form-label">Full Name *</label><input type="text" name="name" value="{{ old('name',$user->name) }}" class="form-input" required></div>
    <div><label class="form-label">Email Address *</label><input type="email" name="email" value="{{ old('email',$user->email) }}" class="form-input" required></div>
    <div><label class="form-label">Phone</label><input type="text" name="phone" value="{{ old('phone',$user->phone) }}" class="form-input"></div>
    <div><label class="form-label">Role *</label>
      <select name="role" class="form-select" required>
        @foreach($roles as $r)<option value="{{ $r->name }}" {{ $user->hasRole($r->name)?'selected':'' }}>{{ $r->name }}</option>@endforeach
      </select>
    </div>
    <div class="flex items-center gap-3">
      <input type="checkbox" name="is_active" value="1" id="isActive" {{ $user->is_active?'checked':'' }} class="rounded text-blue-600">
      <label for="isActive" class="text-sm font-medium text-gray-700">Active</label>
    </div>
    <div class="border-t pt-4"><p class="text-sm text-gray-500 mb-3">Leave blank to keep current password</p>
      <div><label class="form-label">New Password</label><input type="password" name="password" class="form-input" minlength="8"></div>
      <div class="mt-3"><label class="form-label">Confirm Password</label><input type="password" name="password_confirmation" class="form-input"></div>
    </div>
  </div>
</div>
<div class="flex gap-3 mt-4">
  <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Update User</button>
  <a href="{{ route('users.index') }}" class="btn-outline">Cancel</a>
</div>
</form>
</div>
@endsection
