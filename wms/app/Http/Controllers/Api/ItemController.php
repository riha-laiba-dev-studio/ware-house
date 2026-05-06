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
        $items = Item::query()
    ->with(['unit','inventory'])
    ->where(function($q) use ($request) {
        $q->where('name','like','%'.$request->q.'%')
          ->orWhere('sku','like','%'.$request->q.'%')
          ->orWhere('barcode',$request->q);
    })
    ->limit(20)
    ->get()
    ->map(function($item) {
        return [
            'id'             => $item->id,
            'name'           => $item->name,
            'sku'            => $item->sku,
            'unit'           => $item->unit->symbol ?? '',
            'purchase_price' => $item->purchase_price,
            'selling_price'  => $item->selling_price,
            'stock'          => $item->inventory->sum('quantity'),
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
