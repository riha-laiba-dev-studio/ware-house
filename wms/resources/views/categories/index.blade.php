@extends('layouts.app')
@section('title','Categories')
@section('page-title','Categories')

@section('content')
<div class="flex items-center justify-between mb-5">
  <div>
    <p class="text-sm text-gray-500">Manage product categories used across items.</p>
  </div>
  <a href="{{ route('categories.create') }}" class="btn-primary"><i class="fas fa-plus"></i> Add Category</a>
</div>

<div class="card">
  <div class="overflow-x-auto">
    <table class="table">
      <thead>
        <tr>
          <th>Name</th>
          <th class="text-right">Products</th>
        </tr>
      </thead>
      <tbody>
        @forelse($categories as $category)
          <tr>
            <td class="font-medium text-gray-800">{{ $category->name }}</td>
            <td class="text-right text-gray-500">{{ $category->items_count ?? '—' }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="2" class="text-center py-10 text-gray-400">
              <i class="fas fa-boxes-stacked text-3xl mb-2 block"></i> No categories found
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection