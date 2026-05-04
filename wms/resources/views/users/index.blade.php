@extends('layouts.app')
@section('title','Users')
@section('page-title','Manage Users')

@section('content')
<div class="flex justify-end mb-5">
  <a href="{{ route('users.create') }}" class="btn-primary"><i class="fas fa-plus"></i> Add User</a>
</div>
<div class="card">
  <div class="overflow-x-auto">
    <table class="table">
      <thead><tr><th>User</th><th>Email</th><th>Phone</th><th>Role</th><th>Last Login</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        @forelse($users as $u)
        <tr>
          <td>
            <div class="flex items-center gap-3">
              <img src="{{ $u->avatar_url }}" class="w-9 h-9 rounded-full object-cover">
              <div><p class="font-medium text-gray-800">{{ $u->name }}</p><p class="text-xs text-gray-400">#{{ $u->id }}</p></div>
            </div>
          </td>
          <td>{{ $u->email }}</td>
          <td>{{ $u->phone ?: '—' }}</td>
          <td>@foreach($u->roles as $r)<span class="badge badge-info mr-1">{{ $r->name }}</span>@endforeach</td>
          <td class="text-gray-500 text-xs">{{ $u->last_login_at?->diffForHumans() ?? 'Never' }}</td>
          <td><span class="{{ $u->is_active ? 'badge badge-success' : 'badge badge-gray' }}">{{ $u->is_active ? 'Active' : 'Inactive' }}</span></td>
          <td class="flex gap-1">
            <a href="{{ route('users.edit',$u) }}" class="btn-outline btn-sm"><i class="fas fa-pen"></i></a>
            @if($u->id !== auth()->id())
            <form method="POST" action="{{ route('users.destroy',$u) }}">@csrf @method('DELETE')
              <button data-confirm="Delete this user?" class="btn btn-sm text-red-600 border border-red-200 hover:bg-red-50 px-2"><i class="fas fa-trash"></i></button>
            </form>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center py-8 text-gray-400">No users found</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="px-4 py-3 border-t border-gray-100">{{ $users->links() }}</div>
</div>
@endsection
