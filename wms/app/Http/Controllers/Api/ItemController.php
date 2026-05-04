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
        $items = Item::active()->with(['unit','inventory'])
            ->where(fn($q) => $q->where('name','like','%'.$request->q.'%')->orWhere('sku','like','%'.$request->q.'%')->orWhere('barcode',$request->q))
            ->limit(20)->get()
            ->map(fn($item) => [
                'id'             => $item->id,
                'name'           => $item->name,
                'sku'            => $item->sku,
                'unit'           => $item->unit->symbol,
                'purchase_price' => $item->purchase_price,
                'selling_price'  => $item->selling_price,
                'stock'          => $item->inventory->sum('quantity'),
            ]);
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
