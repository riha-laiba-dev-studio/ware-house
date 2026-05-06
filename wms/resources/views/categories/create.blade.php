@extends('layouts.app')
@section('title','Add Category')
@section('page-title','Add Category')

@section('content')
<form method="POST" action="{{ route('categories.store') }}">
  @csrf
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="lg:col-span-2 space-y-4">
      <div class="card">
        <div class="card-header">
          <h3 class="font-semibold text-gray-700">Category Details</h3>
        </div>
        <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="md:col-span-2">
            <label class="form-label">Name *</label>
            <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Electronics" class="form-input" required>
          </div>
        </div>
      </div>
    </div>

    <div class="space-y-4">
      <button type="submit" class="btn-primary w-full justify-center py-3"><i class="fas fa-save"></i> Save Category</button>
      <a href="{{ route('categories.index') }}" class="btn-outline w-full justify-center">Cancel</a>
    </div>
  </div>
</form>
@endsection