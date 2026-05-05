@extends('layouts.app')
@section('title','Login History')
@section('page-title','Login Tracking')

@section('content')
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
  <div class="card p-4 flex items-center gap-3">
    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
      <i class="fas fa-clock-rotate-left text-blue-600"></i>
    </div>
    <div><p class="text-xs text-gray-500">Total Logins</p><p class="text-xl font-bold text-gray-800">{{ number_format($stats['total']) }}</p></div>
  </div>
  <div class="card p-4 flex items-center gap-3">
    <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
      <i class="fas fa-circle-check text-emerald-600"></i>
    </div>
    <div><p class="text-xs text-gray-500">Successful</p><p class="text-xl font-bold text-emerald-700">{{ number_format($stats['success']) }}</p></div>
  </div>
  <div class="card p-4 flex items-center gap-3">
    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
      <i class="fas fa-circle-xmark text-red-600"></i>
    </div>
    <div><p class="text-xs text-gray-500">Failed</p><p class="text-xl font-bold text-red-700">{{ number_format($stats['failed']) }}</p></div>
  </div>
  <div class="card p-4 flex items-center gap-3">
    <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
      <i class="fas fa-calendar-day text-amber-600"></i>
    </div>
    <div><p class="text-xs text-gray-500">Today</p><p class="text-xl font-bold text-amber-700">{{ number_format($stats['today']) }}</p></div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <form class="flex gap-2 flex-wrap">
      <input type="text" name="email" value="{{ request('email') }}" placeholder="Email..." class="form-input w-44">
      <select name="status" class="form-select w-32">
        <option value="">All Status</option>
        <option value="success" {{ request('status')=='success'?'selected':'' }}>Success</option>
        <option value="failed"  {{ request('status')=='failed' ?'selected':'' }}>Failed</option>
      </select>
      <input type="date" name="from" value="{{ request('from') }}" class="form-input">
      <input type="date" name="to"   value="{{ request('to') }}"   class="form-input">
      <button class="btn-primary btn-sm"><i class="fas fa-filter"></i> Filter</button>
      <a href="{{ route('login-logs.index') }}" class="btn-outline btn-sm">Reset</a>
    </form>
  </div>
  <div class="overflow-x-auto">
    <table class="table text-xs">
      <thead>
        <tr><th>Date & Time</th><th>User</th><th>Email</th><th>Status</th><th>IP Address</th><th>Browser / Device</th></tr>
      </thead>
      <tbody>
        @forelse($logs as $log)
        <tr class="hover:bg-gray-50">
          <td class="whitespace-nowrap text-gray-500">{{ $log->created_at->format('d M Y H:i:s') }}</td>
          <td class="font-medium">{{ $log->user->name ?? '—' }}</td>
          <td class="font-mono text-blue-600">{{ $log->email }}</td>
          <td>
            @if($log->status === 'success')
              <span class="badge badge-success"><i class="fas fa-check mr-1"></i>Success</span>
            @else
              <span class="badge badge-danger"><i class="fas fa-times mr-1"></i>Failed</span>
            @endif
          </td>
          <td class="font-mono">{{ $log->ip_address }}</td>
          <td class="max-w-xs truncate text-gray-400" title="{{ $log->user_agent }}">
            @php
              $ua = $log->user_agent ?? '';
              if (str_contains($ua,'Chrome')) $browser = '🌐 Chrome';
              elseif (str_contains($ua,'Firefox')) $browser = '🦊 Firefox';
              elseif (str_contains($ua,'Safari')) $browser = '🧭 Safari';
              elseif (str_contains($ua,'Edge')) $browser = '🔷 Edge';
              else $browser = $ua;
            @endphp
            {{ $browser }}
          </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center py-10 text-gray-400"><i class="fas fa-clock-rotate-left text-3xl mb-2 block opacity-30"></i>No login records found</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($logs->hasPages())
  <div class="px-5 py-3 border-t border-gray-100">{{ $logs->links() }}</div>
  @endif
</div>
@endsection
