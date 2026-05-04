<?php
namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Customer;
use App\Models\Warehouse;
use App\Models\Item;
use App\Models\Inventory;
use App\Services\SaleService;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function __construct(private SaleService $service) {}

    public function index(Request $request)
    {
        $query = Sale::with(['customer','warehouse','creator']);
        if ($request->customer_id) $query->where('customer_id',$request->customer_id);
        if ($request->status)      $query->where('status',$request->status);
        if ($request->from)        $query->whereDate('sale_date','>=',$request->from);
        if ($request->to)          $query->whereDate('sale_date','<=',$request->to);
        $sales      = $query->latest()->paginate(15)->withQueryString();
        $customers  = Customer::active()->get();
        $totalAmount = $query->sum('total_amount');
        $totalDue    = $query->sum('due_amount');
        return view('sales.index', compact('sales','customers','totalAmount','totalDue'));
    }

    public function create()
    {
        $customers  = Customer::active()->get();
        $warehouses = Warehouse::active()->get();
        $items      = Item::active()->with(['unit','inventory','variants'])->get();
        return view('sales.create', compact('customers','warehouses','items'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id'    => 'required|exists:customers,id',
            'warehouse_id'   => 'required|exists:warehouses,id',
            'sale_date'      => 'required|date',
            'discount_amount'=> 'nullable|numeric|min:0',
            'tax_amount'     => 'nullable|numeric|min:0',
            'shipping_cost'  => 'nullable|numeric|min:0',
            'paid_amount'    => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string',
            'notes'          => 'nullable|string',
            'items'          => 'required|array|min:1',
            'items.*.item_id'   => 'required|exists:items,id',
            'items.*.quantity'  => 'required|numeric|min:0.01',
            'items.*.unit_price'=> 'required|numeric|min:0',
        ]);
        $sale = $this->service->create($data);
        return redirect()->route('sales.show', $sale)->with('success','Sale invoice created successfully.');
    }

    public function show(Sale $sale)
    {
        $sale->load(['customer','warehouse','creator','items.item','items.variant','payments','returns']);
        return view('sales.show', compact('sale'));
    }

    public function addPayment(Request $request, Sale $sale)
    {
        $data = $request->validate([
            'amount'         => 'required|numeric|min:0.01|max:'.$sale->due_amount,
            'payment_method' => 'required|in:cash,bank,cheque',
            'payment_date'   => 'required|date',
            'reference'      => 'nullable|string',
            'notes'          => 'nullable|string',
        ]);
        $this->service->addPayment($sale, $data);
        return redirect()->route('sales.show', $sale)->with('success','Payment recorded successfully.');
    }

    public function invoice(Sale $sale)
    {
        $sale->load(['customer','warehouse','items.item','payments']);
        return view('sales.invoice', compact('sale'));
    }
}
