<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Inventory;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function search(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $warehouseIdRaw = $request->input('warehouse_id');
        // When dropdown is searched before selecting warehouse, `warehouse_id` might be empty.
        // Avoid crashing the endpoint; treat it as "no specific warehouse".
        $warehouseId = (is_numeric($warehouseIdRaw) && (string)$warehouseIdRaw !== '')
            ? (int) $warehouseIdRaw
            : null;

        $itemsQuery = Item::query()
            ->active()
            ->with(['unit', 'inventory']);

        if ($q !== '') {
            $itemsQuery->where(function ($qq) use ($q) {
                $qq->where('name', 'like', '%' . $q . '%')
                    ->orWhere('sku', 'like', '%' . $q . '%')
                    ->orWhere('barcode', $q);
            });
        }

        $items = $itemsQuery
            ->orderBy('name')
            ->limit(50)
            ->get()
            ->map(function ($item) use ($warehouseId) {
                $stock = $warehouseId
                    ? ($item->inventory->firstWhere('warehouse_id', $warehouseId)?->quantity ?? 0)
                    : $item->inventory->sum('quantity');

                return [
                    'id'             => $item->id,
                    'name'           => $item->name,
                    'sku'            => $item->sku,
                    'unit'           => $item->unit->symbol ?? '',
                    'purchase_price' => $item->purchase_price,
                    'selling_price'  => $item->selling_price,
                    'stock'          => $stock,
                ];
            });

        return response()->json($items);
    }
    public function stockByWarehouse(Request $request)
    {
        $stock = Inventory::where('item_id', $request->item_id)
            ->where('warehouse_id', $request->warehouse_id)
            ->value('quantity') ?? 0;
        return response()->json(['stock' => $stock]);
    }
}
