<?php
namespace App\Http\Controllers;

use App\Models\InventoryMovement;
use App\Models\Item;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class InventoryMovementController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryMovement::with(['item','warehouse','creator'])
            ->orderByDesc('movement_date');

        if ($request->item_id)      $query->where('item_id', $request->item_id);
        if ($request->warehouse_id) $query->where('warehouse_id', $request->warehouse_id);
        if ($request->type)         $query->where('type', $request->type);
        if ($request->from)         $query->whereDate('movement_date', '>=', $request->from);
        if ($request->to)           $query->whereDate('movement_date', '<=', $request->to);

        $movements  = $query->paginate(50)->withQueryString();
        $items      = Item::active()->orderBy('name')->get();
        $warehouses = Warehouse::active()->get();

        $typeLabels = [
            'purchase'     => ['label'=>'Purchase IN',    'color'=>'badge-success'],
            'sale'         => ['label'=>'Sale OUT',       'color'=>'badge-danger'],
            'transfer_in'  => ['label'=>'Transfer IN',    'color'=>'badge-info'],
            'transfer_out' => ['label'=>'Transfer OUT',   'color'=>'badge-warning'],
            'adjustment'   => ['label'=>'Adjustment',     'color'=>'badge-gray'],
            'return_in'    => ['label'=>'Return IN',      'color'=>'text-teal-600 bg-teal-50'],
            'return_out'   => ['label'=>'Return OUT',     'color'=>'text-orange-600 bg-orange-50'],
            'opening'      => ['label'=>'Opening Stock',  'color'=>'badge-gray'],
        ];

        return view('inventory-movements.index', compact('movements','items','warehouses','typeLabels'));
    }
}
