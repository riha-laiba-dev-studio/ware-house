@extends('layouts.app')
@section('title','Items')
@section('page-title','Product Listing')

@section('content')
<div class="flex items-center justify-between mb-5">
  <div class="flex items-center gap-3">
    <form class="flex gap-2">
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Search items..." class="form-input w-48">
      <select name="category_id" class="form-select w-40">
        <option value="">All Categories</option>
        @foreach($categories as $c)<option value="{{ $c->id }}" {{ request('category_id')==$c->id?'selected':'' }}>{{ $c->name }}</option>@endforeach
      </select>
      <button class="btn-primary btn-sm">Filter</button>
      <a href="{{ route('items.index') }}" class="btn-outline btn-sm">Reset</a>
    </form>
  </div>
  <a href="{{ route('items.create') }}" class="btn-primary"><i class="fas fa-plus"></i> Add Item</a>
</div>

<div class="card">
  <div class="overflow-x-auto">
    <table class="table">
      <thead>
        <tr><th>SKU</th><th>Item Name</th><th>Category</th><th>Unit</th><th>Purchase</th><th>Selling</th><th>Stock</th><th>Alert</th><th>Status</th><th>Actions</th></tr>
      </thead>
      <tbody>
        @forelse($items as $item)
        <tr>
          <td class="font-mono text-xs text-blue-600">{{ $item->sku }}</td>
          <td>
            <div class="flex items-center gap-3">
              <img src="{{ $item->image_url }}" class="w-9 h-9 rounded-lg object-cover border border-gray-200">
              <div>
                <p class="font-medium text-gray-800">{{ $item->name }}</p>
                <p class="text-xs text-gray-400">{{ $item->brand?->name }}</p>
              </div>
            </div>
          </td>
          <td>{{ $item->category->name }}</td>
          <td>{{ $item->unit->symbol }}</td>
          <td class="font-medium">PKR {{ number_format($item->purchase_price,2) }}</td>
          <td class="font-medium text-blue-600">PKR {{ number_format($item->selling_price,2) }}</td>
          <td>
            <span class="{{ $item->inventory_sum_quantity <= $item->alert_quantity ? 'badge badge-danger' : 'badge badge-success' }}">
              {{ number_format($item->inventory_sum_quantity ?? 0, 2) }}
            </span>
          </td>
          <td class="text-gray-500">{{ $item->alert_quantity }}</td>
          <td>
            <span class="{{ $item->is_active ? 'badge badge-success' : 'badge badge-gray' }}">
              {{ $item->is_active ? 'Active' : 'Inactive' }}
            </span>
          </td>
          <td>
            <div class="flex items-center gap-1">
              <a href="{{ route('items.show',$item) }}" class="btn btn-sm text-blue-600 hover:text-blue-800 px-2"><i class="fas fa-eye"></i></a>
              <a href="{{ route('items.edit',$item) }}" class="btn btn-sm text-amber-600 hover:text-amber-800 px-2"><i class="fas fa-pen"></i></a>
              <form method="POST" action="{{ route('items.destroy',$item) }}" class="inline">
                @csrf @method('DELETE')
                <button data-confirm="Delete this item?" class="btn btn-sm text-red-600 hover:text-red-800 px-2"><i class="fas fa-trash"></i></button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="10" class="text-center py-10 text-gray-400"><i class="fas fa-boxes text-3xl mb-2 block"></i> No items found</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="px-4 py-3 border-t border-gray-100">{{ $items->links() }}</div>
</div>
@endsection
