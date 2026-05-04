<?php
namespace App\Http\Controllers;

use App\Models\InventoryAdjustment;
use App\Models\InventoryAdjustmentItem;
use App\Models\Warehouse;
use App\Models\Item;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InventoryAdjustmentController extends Controller
{
    public function __construct(private InventoryService $inventory) {}
    public function index()
    {
        $adjustments = InventoryAdjustment::with(['warehouse','creator'])->latest()->paginate(15);
        return view('inventory-adjustments.index', compact('adjustments'));
    }
    public function create()
    {
        $warehouses = Warehouse::active()->get();
        $items = Item::active()->with(['unit','inventory'])->get();
        return view('inventory-adjustments.create', compact('warehouses','items'));
    }
    public function store(Request $request)
    {
        $data = $request->validate(['warehouse_id'=>'required|exists:warehouses,id','adjustment_date'=>'required|date','type'=>'required|in:manual,damage,loss,found','notes'=>'nullable|string','items'=>'required|array|min:1','items.*.item_id'=>'required|exists:items,id','items.*.adjusted_quantity'=>'required|numeric|min:0','items.*.reason'=>'nullable|string']);
        DB::transaction(function () use ($data) {
            $adj = InventoryAdjustment::create(['reference'=>'ADJ-'.strtoupper(Str::random(8)),'warehouse_id'=>$data['warehouse_id'],'created_by'=>auth()->id(),'adjustment_date'=>$data['adjustment_date'],'type'=>$data['type'],'status'=>'approved','notes'=>$data['notes']??null]);
            foreach ($data['items'] as $row) {
                $current = $this->inventory->getOrCreateInventory($row['item_id'],$data['warehouse_id'])->quantity;
                $diff = $row['adjusted_quantity'] - $current;
                InventoryAdjustmentItem::create(['inventory_adjustment_id'=>$adj->id,'item_id'=>$row['item_id'],'current_quantity'=>$current,'adjusted_quantity'=>$row['adjusted_quantity'],'difference'=>$diff,'reason'=>$row['reason']??null]);
                $this->inventory->adjustStock($row['item_id'],$data['warehouse_id'],$row['adjusted_quantity'],0,InventoryAdjustment::class,$adj->id,null,$row['reason']??null);
            }
        });
        return redirect()->route('inventory-adjustments.index')->with('success','Inventory adjusted successfully.');
    }
    public function show(InventoryAdjustment $inventoryAdjustment)
    {
        $inventoryAdjustment->load(['warehouse','creator','items.item']);
        return view('inventory-adjustments.show', compact('inventoryAdjustment'));
    }
}
