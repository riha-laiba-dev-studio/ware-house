<?php
namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleReturnController extends Controller
{
    public function index()
    {
        $returns = SaleReturn::with(['sale','customer','warehouse','creator'])
            ->latest()->paginate(25);
        return view('sale-returns.index', compact('returns'));
    }

    public function create()
    {
        $sales = Sale::with('customer')->where('status','confirmed')->latest()->get();
        return view('sale-returns.create', compact('sales'));
    }

    public function getSaleItems(Sale $sale)
    {
        $sale->load(['items.item.unit','warehouse']);
        return response()->json($sale);
    }

    public function store(Request $request)
    {
        $request->validate([
            'sale_id'       => 'required|exists:sales,id',
            'return_date'   => 'required|date',
            'reason'        => 'nullable|string|max:500',
            'items'         => 'required|array|min:1',
            'items.*.item_id'   => 'required|exists:items,id',
            'items.*.quantity'  => 'required|numeric|min:0.01',
            'items.*.unit_price'=> 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $sale = Sale::findOrFail($request->sale_id);
            $total = collect($request->items)->sum(fn($i) => $i['quantity'] * $i['unit_price']);

            $ref = 'SRN-' . date('ymd') . '-' . str_pad(SaleReturn::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

            $return = SaleReturn::create([
                'reference'    => $ref,
                'sale_id'      => $sale->id,
                'customer_id'  => $sale->customer_id,
                'warehouse_id' => $sale->warehouse_id,
                'created_by'   => auth()->id(),
                'return_date'  => $request->return_date,
                'status'       => 'approved',
                'total_amount' => $total,
                'reason'       => $request->reason,
            ]);

            foreach ($request->items as $item) {
                SaleReturnItem::create([
                    'sale_return_id' => $return->id,
                    'item_id'        => $item['item_id'],
                    'quantity'       => $item['quantity'],
                    'unit_price'     => $item['unit_price'],
                    'subtotal'       => $item['quantity'] * $item['unit_price'],
                ]);

                // Restore stock
                $inv = Inventory::firstOrCreate(
                    ['item_id' => $item['item_id'], 'warehouse_id' => $sale->warehouse_id],
                    ['quantity' => 0]
                );
                $before = $inv->quantity;
                $inv->increment('quantity', $item['quantity']);

                InventoryMovement::create([
                    'item_id'         => $item['item_id'],
                    'warehouse_id'    => $sale->warehouse_id,
                    'type'            => 'return_in',
                    'quantity'        => $item['quantity'],
                    'before_quantity' => $before,
                    'after_quantity'  => $before + $item['quantity'],
                    'unit_cost'       => $item['unit_price'],
                    'reference_type'  => SaleReturn::class,
                    'reference_id'    => $return->id,
                    'created_by'      => auth()->id(),
                    'notes'           => 'Sale return: ' . $ref,
                    'movement_date'   => now(),
                ]);
            }
        });

        return redirect()->route('sale-returns.index')->with('success', 'Sale return recorded successfully. Stock has been restored.');
    }

    public function show(SaleReturn $saleReturn)
    {
        $saleReturn->load(['sale','customer','warehouse','creator','items.item.unit']);
        return view('sale-returns.show', compact('saleReturn'));
    }
}
