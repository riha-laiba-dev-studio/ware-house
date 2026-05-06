<?php
namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\Item;
use App\Services\PurchaseService;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function __construct(private PurchaseService $service) {}

    public function index(Request $request)
    {
        $query = Purchase::with(['supplier','warehouse','creator']);
        if ($request->supplier_id) $query->where('supplier_id',$request->supplier_id);
        if ($request->status)      $query->where('status',$request->status);
        if ($request->from)        $query->whereDate('purchase_date','>=',$request->from);
        if ($request->to)          $query->whereDate('purchase_date','<=',$request->to);
        $purchases  = $query->latest()->paginate(15)->withQueryString();
        $suppliers  = Supplier::active()->get();
        $totalAmount = $query->sum('total_amount');
        $totalDue    = $query->sum('due_amount');
        return view('purchases.index', compact('purchases','suppliers','totalAmount','totalDue'));
    }

    public function create()
    {
        $suppliers  = Supplier::active()->get();
        $warehouses = Warehouse::active()->get();
        // Preload only what the purchase "items" dropdown needs.
        // Keeps JSON small and avoids frontend parse issues.
        $items = Item::active()
            ->with(['unit', 'inventory'])
            ->get()
            ->map(function (Item $item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'sku' => $item->sku,
                    'barcode' => $item->barcode,
                    'purchase_price' => $item->purchase_price,
                    'unit' => ['symbol' => $item->unit?->symbol ?? ''],
                    'inventory' => $item->inventory->map(function ($inv) {
                        return [
                            'warehouse_id' => $inv->warehouse_id,
                            'quantity' => $inv->quantity,
                        ];
                    })->values(),
                ];
            });
        return view('purchases.create', compact('suppliers','warehouses','items'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id'    => 'required|exists:suppliers,id',
            'warehouse_id'   => 'required|exists:warehouses,id',
            'purchase_date'  => 'required|date',
            'shipping_cost'  => 'nullable|numeric|min:0',
            'notes'          => 'nullable|string',
            'items'          => 'required|array|min:1',
            'items.*.item_id'=> 'required|exists:items,id',
            'items.*.quantity'  => 'required|numeric|min:0.01',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);
        $purchase = $this->service->create($data);
        return redirect()->route('purchases.show', $purchase)->with('success','Purchase order created successfully.');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier','warehouse','creator','items.item','items.variant','payments']);
        return view('purchases.show', compact('purchase'));
    }

    public function receive(Purchase $purchase)
    {
        $this->service->receive($purchase);
        return redirect()->route('purchases.show', $purchase)->with('success','Stock received successfully.');
    }

    public function addPayment(Request $request, Purchase $purchase)
    {
        $data = $request->validate([
            'amount'         => 'required|numeric|min:0.01|max:'.$purchase->due_amount,
            'payment_method' => 'required|in:cash,bank,cheque',
            'payment_date'   => 'required|date',
            'reference'      => 'nullable|string',
            'notes'          => 'nullable|string',
        ]);
        $this->service->addPayment($purchase, $data);
        return redirect()->route('purchases.show', $purchase)->with('success','Payment recorded successfully.');
    }
}
