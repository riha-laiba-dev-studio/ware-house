@extends('layouts.app')
@section('title','Units')
@section('page-title','Units')

@section('content')
<div class="flex items-center justify-between mb-5">
  <div>
    <p class="text-sm text-gray-500">Units are used for stock, purchases and sales (e.g. kg, pcs).</p>
  </div>
  <a href="{{ route('units.create') }}" class="btn-primary"><i class="fas fa-plus"></i> Add Unit</a>
</div>

<div class="card">
  <div class="overflow-x-auto">
    <table class="table">
      <thead>
        <tr>
          <th>Name</th>
          <th>Symbol</th>
        </tr>
      </thead>
      <tbody>
        @forelse($units as $unit)
          <tr>
            <td class="font-medium text-gray-800">{{ $unit->name }}</td>
            <td class="font-mono text-xs text-blue-600">{{ $unit->symbol }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="2" class="text-center py-10 text-gray-400">
              <i class="fas fa-ruler-combined text-3xl mb-2 block"></i> No units found
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection