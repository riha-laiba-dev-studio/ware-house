<?php
namespace App\Services;

use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use Illuminate\Support\Facades\DB;

class StockTransferService
{
    public function __construct(private InventoryService $inventory) {}

    public function create(array $data): StockTransfer
    {
        return DB::transaction(function () use ($data) {
            $transfer = StockTransfer::create([
                'reference'        => 'ST-'.date('Ymd').'-'.str_pad(StockTransfer::whereDate('created_at',today())->count()+1,4,'0',STR_PAD_LEFT),
                'from_warehouse_id'=> $data['from_warehouse_id'],
                'to_warehouse_id'  => $data['to_warehouse_id'],
                'created_by'       => auth()->id(),
                'transfer_date'    => $data['transfer_date'],
                'status'           => 'pending',
                'notes'            => $data['notes'] ?? null,
            ]);
            foreach ($data['items'] as $row) {
                StockTransferItem::create([
                    'stock_transfer_id' => $transfer->id,
                    'item_id'           => $row['item_id'],
                    'item_variant_id'   => $row['item_variant_id'] ?? null,
                    'quantity'          => $row['quantity'],
                    'received_quantity' => 0,
                    'unit_cost'         => $row['unit_cost'] ?? 0,
                ]);
            }
            return $transfer->fresh();
        });
    }

    public function approve(StockTransfer $transfer): StockTransfer
    {
        return DB::transaction(function () use ($transfer) {
            if ($transfer->status !== 'pending') throw new \Exception('Only pending transfers can be approved.');
            foreach ($transfer->items as $item) {
                $this->inventory->decreaseStock($item->item_id,$transfer->from_warehouse_id,$item->quantity,$item->unit_cost,'transfer_out',StockTransfer::class,$transfer->id,$item->item_variant_id);
                $this->inventory->increaseStock($item->item_id,$transfer->to_warehouse_id,$item->quantity,$item->unit_cost,'transfer_in',StockTransfer::class,$transfer->id,$item->item_variant_id);
                $item->update(['received_quantity'=>$item->quantity]);
            }
            $transfer->update(['status'=>'completed']);
            return $transfer->fresh();
        });
    }
}
