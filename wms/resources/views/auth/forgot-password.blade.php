<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password — WMS Pro</title>
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
      <p class="text-blue-300 text-sm mt-1">Recover your account</p>
    </div>

<div class="bg-white rounded-2xl shadow-2xl p-8">
      <h2 class="text-xl font-semibold text-gray-800 mb-2">Forgot password?</h2>
      <p class="text-sm text-gray-500 mb-6">Enter your email and we’ll send you a reset link.</p>
      @if(session('success'))
      <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm">
        {{ session('success') }}
      </div>
      @endif
      @if($errors->any())
      <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
        {{ $errors->first() }}
      </div>
      @endif

      <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf
        <div>
          <label class="form-label">Email Address</label>
          <div class="relative">
            <i class="fas fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="email" name="email" value="{{ old('email') }}" class="form-input pl-9" placeholder="you@example.com" required autofocus>
          </div>
        </div>
        <button type="submit" class="w-full btn-primary justify-center py-3 text-base">
          <i class="fas fa-paper-plane"></i> Send Reset Link
        </button>
      </form>
      <div class="mt-6 flex items-center justify-between text-sm">
        <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-800 font-medium">
          <i class="fas fa-arrow-left mr-1"></i> Back to login
        </a>
        <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-700 font-semibold">
          Create account
        </a>
      </div>
    </div>
    <p class="text-center text-blue-300 text-xs mt-6">© {{ date('Y') }} WMS Pro. All rights reserved.</p>
  </div>
</body>
</html>
