@extends('layouts.app')
@section('title','Add Unit')
@section('page-title','Add Unit')

@section('content')
<form method="POST" action="{{ route('units.store') }}">
  @csrf
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="lg:col-span-2 space-y-4">
      <div class="card">
        <div class="card-header">
          <h3 class="font-semibold text-gray-700">Unit Details</h3>
        </div>
        <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="form-label">Name *</label>
            <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Kilogram" class="form-input" required>
          </div>
          <div>
            <label class="form-label">Symbol *</label>
            <input type="text" name="symbol" value="{{ old('symbol') }}" placeholder="e.g. kg" class="form-input" required>
          </div>
        </div>
      </div>
    </div>

    <div class="space-y-4">
      <button type="submit" class="btn-primary w-full justify-center py-3"><i class="fas fa-save"></i> Save Unit</button>
      <a href="{{ route('units.index') }}" class="btn-outline w-full justify-center">Cancel</a>
    </div>
  </div>
</form>
@endsection