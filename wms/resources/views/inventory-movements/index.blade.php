@extends('layouts.app')
@section('title','Stock Movement Log')
@section('page-title','Stock Movement Log')

@section('content')
<div class="flex items-center justify-between mb-5 flex-wrap gap-3">
  <form class="flex gap-2 flex-wrap">
    <select name="item_id" class="form-select w-44">
      <option value="">All Items</option>
      @foreach($items as $i)<option value="{{ $i->id }}" {{ request('item_id')==$i->id?'selected':'' }}>{{ $i->name }}</option>@endforeach
    </select>
    <select name="warehouse_id" class="form-select w-36">
      <option value="">All Warehouses</option>
      @foreach($warehouses as $w)<option value="{{ $w->id }}" {{ request('warehouse_id')==$w->id?'selected':'' }}>{{ $w->name }}</option>@endforeach
    </select>
    <select name="type" class="form-select w-36">
      <option value="">All Types</option>
      @foreach($typeLabels as $key => $t)<option value="{{ $key }}" {{ request('type')==$key?'selected':'' }}>{{ $t['label'] }}</option>@endforeach
    </select>
    <input type="date" name="from" value="{{ request('from') }}" class="form-input" placeholder="From">
    <input type="date" name="to"   value="{{ request('to') }}"   class="form-input" placeholder="To">
    <div class="flex items-center gap-2">
      <button class="btn-primary btn-sm"><i class="fas fa-filter"></i> Filter</button>
      <a href="{{ route('inventory-movements.index') }}" class="btn-outline btn-sm">Reset</a>
    </div>
  </form>
  <button onclick="window.print()" class="btn-outline btn-sm"><i class="fas fa-print text-blue-500"></i> Print</button>
</div>

<div class="card">
  <div class="card-header">
    <h3 class="font-semibold text-gray-700">Movement History</h3>
    <span class="badge badge-gray text-xs">{{ $movements->total() }} records</span>
  </div>
  <div class="overflow-x-auto">
    <table class="table text-xs">
      <thead>
        <tr>
          <th>Date & Time</th>
          <th>Item</th>
          <th>SKU</th>
          <th>Warehouse</th>
          <th>Type</th>
          <th class="text-right">Qty</th>
          <th class="text-right">Before</th>
          <th class="text-right">After</th>
          <th>Reference</th>
          <th>Notes</th>
          <th>By</th>
        </tr>
      </thead>
      <tbody>
        @forelse($movements as $m)
        <tr class="hover:bg-gray-50">
          <td class="whitespace-nowrap text-gray-500">{{ $m->movement_date->format('d M Y H:i') }}</td>
          <td class="font-medium text-gray-800">{{ $m->item->name }}</td>
          <td class="font-mono text-blue-600">{{ $m->item->sku }}</td>
          <td>{{ $m->warehouse->name }}</td>
          <td>
            @php $t = $typeLabels[$m->type] ?? ['label'=>ucfirst($m->type),'color'=>'badge-gray']; @endphp
            <span class="badge {{ $t['color'] }} text-xs">{{ $t['label'] }}</span>
          </td>
          <td class="text-right font-bold {{ in_array($m->type,['purchase','transfer_in','return_in','opening','adjustment']) ? 'text-emerald-600' : 'text-red-600' }}">
            {{ in_array($m->type,['purchase','transfer_in','return_in','opening','adjustment']) ? '+' : '-' }}{{ number_format(abs($m->quantity),2) }}
          </td>
          <td class="text-right text-gray-500">{{ number_format($m->before_quantity,2) }}</td>
          <td class="text-right font-semibold">{{ number_format($m->after_quantity,2) }}</td>
          <td class="font-mono text-blue-600 text-xs">
            @if($m->reference_type && $m->reference_id)
              {{ class_basename($m->reference_type) }}#{{ $m->reference_id }}
            @else —
            @endif
          </td>
          <td class="text-gray-500 max-w-32 truncate" title="{{ $m->notes }}">{{ $m->notes ?? '—' }}</td>
          <td class="text-gray-500">{{ $m->creator->name ?? '—' }}</td>
        </tr>
        @empty
        <tr><td colspan="11" class="text-center py-10 text-gray-400"><i class="fas fa-chart-line text-3xl mb-2 block opacity-30"></i>No movement records found</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($movements->hasPages())
  <div class="px-5 py-3 border-t border-gray-100">{{ $movements->links() }}</div>
  @endif
</div>
@endsection
