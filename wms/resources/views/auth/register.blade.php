<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register — WMS Pro</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-800 flex items-center justify-center p-4">
  <div class="w-full max-w-md">
    <div class="text-center mb-8">
      <div class="inline-flex w-16 h-16 bg-blue-600 rounded-2xl items-center justify-center mb-4 shadow-lg shadow-blue-500/30">
        <i class="fas fa-warehouse text-white text-2xl"></i>
      </div>
      <h1 class="text-white text-2xl font-bold">WMS Pro</h1>
      <p class="text-blue-300 text-sm mt-1">Create your account</p>
    </div>

    <div class="bg-white rounded-2xl shadow-2xl p-8">
      <h2 class="text-xl font-semibold text-gray-800 mb-6">Register</h2>

      @if($errors->any())
      <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
        {{ $errors->first() }}
      </div>
      @endif

      <form method="POST" action="{{ route('register.store') }}" class="space-y-5">
        @csrf
        <div>
          <label class="form-label">Full Name</label>
          <div class="relative">
            <i class="fas fa-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" name="name" value="{{ old('name') }}" class="form-input pl-9" placeholder="Your name" required autofocus>
          </div>
        </div>
        <div>
          <label class="form-label">Email Address</label>
          <div class="relative">
            <i class="fas fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="email" name="email" value="{{ old('email') }}" class="form-input pl-9" placeholder="you@example.com" required>
          </div>
        </div>
        <div>
          <label class="form-label">Phone (optional)</label>
          <div class="relative">
            <i class="fas fa-phone absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" name="phone" value="{{ old('phone') }}" class="form-input pl-9" placeholder="+92 3xx xxxxxxx">
          </div>
        </div>
        <div>
          <label class="form-label">Password</label>
          <div class="relative">
            <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="password" name="password" class="form-input pl-9 pr-10" placeholder="••••••••" required>
          </div>
          <p class="text-xs text-gray-400 mt-1">Minimum 8 characters.</p>
        </div>
        <div>
          <label class="form-label">Confirm Password</label>
          <div class="relative">
            <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="password" name="password_confirmation" class="form-input pl-9" placeholder="••••••••" required>
          </div>
        </div>

        <button type="submit" class="w-full btn-primary justify-center py-3 text-base">
          <i class="fas fa-user-plus"></i> Create Account
        </button>
      </form>

      <div class="mt-6 text-center text-sm text-gray-600">
        Already have an account?
        <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-semibold">Sign in</a>
      </div>
    </div>

    <p class="text-center text-blue-300 text-xs mt-6">© {{ date('Y') }} WMS Pro. All rights reserved.</p>
  </div>
</body>
</html>

