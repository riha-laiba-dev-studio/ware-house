@extends('layouts.app')
@section('title','Warehouses')
@section('page-title','Manage Warehouses')

@section('content')
<div class="flex justify-end mb-5">
  <a href="{{ route('warehouses.create') }}" class="btn-primary"><i class="fas fa-plus"></i> Add Warehouse</a>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
  @forelse($warehouses as $w)
  <div class="card p-5">
    <div class="flex items-start justify-between mb-3">
      <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center"><i class="fas fa-warehouse text-blue-600"></i></div>
      <span class="{{ $w->is_active ? 'badge badge-success' : 'badge badge-gray' }}">{{ $w->is_active ? 'Active' : 'Inactive' }}</span>
    </div>
    <h3 class="font-bold text-gray-800">{{ $w->name }}</h3>
    <p class="text-xs font-mono text-blue-600 mb-2">{{ $w->code }}</p>
    @if($w->city)<p class="text-xs text-gray-500"><i class="fas fa-location-dot mr-1"></i>{{ $w->city }}</p>@endif
    @if($w->phone)<p class="text-xs text-gray-500"><i class="fas fa-phone mr-1"></i>{{ $w->phone }}</p>@endif
    @if($w->manager)<p class="text-xs text-gray-500"><i class="fas fa-user mr-1"></i>{{ $w->manager->name }}</p>@endif
    <p class="text-xs text-gray-400 mt-2">{{ $w->inventory_count }} item types</p>
    <div class="flex gap-2 mt-4 pt-4 border-t border-gray-100">
      <a href="{{ route('warehouses.edit',$w) }}" class="btn-outline btn-sm flex-1 justify-center"><i class="fas fa-pen"></i> Edit</a>
      <form method="POST" action="{{ route('warehouses.destroy',$w) }}">
        @csrf @method('DELETE')
        <button data-confirm="Delete this warehouse?" class="btn-danger btn-sm"><i class="fas fa-trash"></i></button>
      </form>
    </div>
  </div>
  @empty
  <div class="col-span-3 text-center py-12 text-gray-400"><i class="fas fa-warehouse text-5xl mb-3 block"></i><p>No warehouses yet</p><a href="{{ route('warehouses.create') }}" class="btn-primary mt-4 inline-flex">Add First Warehouse</a></div>
  @endforelse
</div>
<div class="mt-4">{{ $warehouses->links() }}</div>
@endsection
