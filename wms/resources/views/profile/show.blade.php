@extends('layouts.app')
@section('title','Profile')
@section('page-title','My Profile')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
  <div class="space-y-4">
    <div class="card p-5 text-center">
      <img src="{{ $user->avatar_url }}" class="w-24 h-24 rounded-2xl object-cover mx-auto border border-gray-200">
      <p class="mt-3 font-bold text-gray-800">{{ $user->name }}</p>
      <p class="text-xs text-gray-500">{{ $user->email }}</p>
      <p class="text-xs text-gray-400 mt-1">{{ $user->getRoleNames()->first() ?? 'User' }}</p>
      <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-3 text-xs text-blue-800 text-left">
        <i class="fas fa-circle-info mr-1"></i>
        Avatar is auto-generated unless you add upload support later.
      </div>
    </div>
  </div>

  <div class="lg:col-span-2 space-y-4">
    <div class="card">
      <div class="card-header">
        <h3 class="font-semibold text-gray-700">Profile Details</h3>
      </div>
      <form method="POST" action="{{ route('profile.update') }}">
        @csrf
        @method('PUT')
        <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="md:col-span-2">
            <label class="form-label">Full Name *</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-input" required>
          </div>
          <div>
            <label class="form-label">Email *</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-input" required>
          </div>
          <div>
            <label class="form-label">Phone</label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-input" placeholder="+92 3xx xxxxxxx">
          </div>
        </div>
        <div class="px-5 pb-5">
          <button class="btn-primary"><i class="fas fa-save"></i> Save Changes</button>
        </div>
      </form>
    </div>

    <div class="card">
      <div class="card-header">
        <h3 class="font-semibold text-gray-700">Change Password</h3>
      </div>
      <form method="POST" action="{{ route('profile.password') }}">
        @csrf
        @method('PUT')
        <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="md:col-span-2">
            <label class="form-label">Current Password *</label>
            <input type="password" name="current_password" class="form-input" required>
          </div>
          <div>
            <label class="form-label">New Password *</label>
            <input type="password" name="password" class="form-input" required>
            <p class="text-xs text-gray-400 mt-1">Minimum 8 characters.</p>
          </div>
          <div>
            <label class="form-label">Confirm New Password *</label>
            <input type="password" name="password_confirmation" class="form-input" required>
          </div>
        </div>
        <div class="px-5 pb-5">
          <button class="btn-outline"><i class="fas fa-key"></i> Update Password</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

