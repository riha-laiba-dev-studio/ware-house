<?php
namespace App\Http\Controllers;

use App\Models\StockTransfer;
use App\Models\Warehouse;
use App\Models\Item;
use App\Services\StockTransferService;
use Illuminate\Http\Request;

class StockTransferController extends Controller
{
    public function __construct(private StockTransferService $service) {}
    public function index()
    {
        $transfers = StockTransfer::with(['fromWarehouse','toWarehouse','creator'])->latest()->paginate(15);
        return view('stock-transfers.index', compact('transfers'));
    }
    public function create()
    {
        $warehouses = Warehouse::active()->get();
        $items      = Item::active()->with(['unit','inventory'])->get();
        return view('stock-transfers.create', compact('warehouses','items'));
    }
    public function store(Request $request)
    {
        $data = $request->validate(['from_warehouse_id'=>'required|exists:warehouses,id','to_warehouse_id'=>'required|exists:warehouses,id|different:from_warehouse_id','transfer_date'=>'required|date','notes'=>'nullable|string','items'=>'required|array|min:1','items.*.item_id'=>'required|exists:items,id','items.*.quantity'=>'required|numeric|min:0.01']);
        $transfer = $this->service->create($data);
        return redirect()->route('stock-transfers.show', $transfer)->with('success','Stock transfer created.');
    }
    public function show(StockTransfer $stockTransfer)
    {
        $stockTransfer->load(['fromWarehouse','toWarehouse','creator','items.item','items.variant']);
        return view('stock-transfers.show', compact('stockTransfer'));
    }
    public function approve(StockTransfer $stockTransfer)
    {
        $this->service->approve($stockTransfer);
        return redirect()->route('stock-transfers.show', $stockTransfer)->with('success','Transfer approved and stock moved.');
    }
}
