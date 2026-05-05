<?php
namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseReturnController extends Controller
{
    public function index()
    {
        $returns = PurchaseReturn::with(['purchase','supplier','warehouse','creator'])
            ->latest()->paginate(25);
        return view('purchase-returns.index', compact('returns'));
    }

    public function create()
    {
        $purchases = Purchase::with('supplier')->where('status','received')->latest()->get();
        return view('purchase-returns.create', compact('purchases'));
    }

    public function getPurchaseItems(Purchase $purchase)
    {
        $purchase->load(['items.item.unit','warehouse']);
        return response()->json($purchase);
    }

    public function store(Request $request)
    {
        $request->validate([
            'purchase_id'   => 'required|exists:purchases,id',
            'return_date'   => 'required|date',
            'reason'        => 'nullable|string|max:500',
            'items'         => 'required|array|min:1',
            'items.*.item_id'  => 'required|exists:items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_cost'=> 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $purchase = Purchase::findOrFail($request->purchase_id);
            $total = collect($request->items)->sum(fn($i) => $i['quantity'] * $i['unit_cost']);

            $ref = 'PRN-' . date('ymd') . '-' . str_pad(PurchaseReturn::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

            $return = PurchaseReturn::create([
                'reference'    => $ref,
                'purchase_id'  => $purchase->id,
                'supplier_id'  => $purchase->supplier_id,
                'warehouse_id' => $purchase->warehouse_id,
                'created_by'   => auth()->id(),
                'return_date'  => $request->return_date,
                'status'       => 'approved',
                'total_amount' => $total,
                'reason'       => $request->reason,
            ]);

            foreach ($request->items as $item) {
                PurchaseReturnItem::create([
                    'purchase_return_id' => $return->id,
                    'item_id'            => $item['item_id'],
                    'quantity'           => $item['quantity'],
                    'unit_cost'          => $item['unit_cost'],
                    'subtotal'           => $item['quantity'] * $item['unit_cost'],
                ]);

                // Deduct stock (returning to supplier)
                $inv = Inventory::where(['item_id' => $item['item_id'], 'warehouse_id' => $purchase->warehouse_id])->first();
                if ($inv) {
                    $before = $inv->quantity;
                    $inv->decrement('quantity', $item['quantity']);

                    InventoryMovement::create([
                        'item_id'         => $item['item_id'],
                        'warehouse_id'    => $purchase->warehouse_id,
                        'type'            => 'return_out',
                        'quantity'        => $item['quantity'],
                        'before_quantity' => $before,
                        'after_quantity'  => $before - $item['quantity'],
                        'unit_cost'       => $item['unit_cost'],
                        'reference_type'  => PurchaseReturn::class,
                        'reference_id'    => $return->id,
                        'created_by'      => auth()->id(),
                        'notes'           => 'Purchase return: ' . $ref,
                        'movement_date'   => now(),
                    ]);
                }
            }
        });

        return redirect()->route('purchase-returns.index')->with('success', 'Purchase return recorded successfully. Stock has been deducted.');
    }

    public function show(PurchaseReturn $purchaseReturn)
    {
        $purchaseReturn->load(['purchase','supplier','warehouse','creator','items.item.unit']);
        return view('purchase-returns.show', compact('purchaseReturn'));
    }
}
