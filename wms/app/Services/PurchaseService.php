<?php
namespace App\Services;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchasePayment;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseService
{
    public function __construct(private InventoryService $inventory) {}

    public function create(array $data): Purchase
    {
        return DB::transaction(function () use ($data) {
            $purchase = Purchase::create([
                'reference'    => $this->generateReference(),
                'supplier_id'  => $data['supplier_id'],
                'warehouse_id' => $data['warehouse_id'],
                'created_by'   => auth()->id(),
                'purchase_date'=> $data['purchase_date'],
                'status'       => 'pending',
                'notes'        => $data['notes'] ?? null,
                'subtotal'     => 0, 'discount_amount' => 0,
                'tax_amount'   => 0, 'shipping_cost'   => $data['shipping_cost'] ?? 0,
                'total_amount' => 0, 'paid_amount'     => 0, 'due_amount' => 0,
                'payment_status' => 'unpaid',
            ]);

            $subtotal = 0;
            foreach ($data['items'] as $row) {
                $itemSubtotal = ($row['quantity'] * $row['unit_cost']) - ($row['discount_amount'] ?? 0) + ($row['tax_amount'] ?? 0);
                PurchaseItem::create([
                    'purchase_id'     => $purchase->id,
                    'item_id'         => $row['item_id'],
                    'item_variant_id' => $row['item_variant_id'] ?? null,
                    'quantity'        => $row['quantity'],
                    'received_quantity'=> 0,
                    'unit_cost'       => $row['unit_cost'],
                    'discount_percent'=> $row['discount_percent'] ?? 0,
                    'discount_amount' => $row['discount_amount'] ?? 0,
                    'tax_percent'     => $row['tax_percent'] ?? 0,
                    'tax_amount'      => $row['tax_amount'] ?? 0,
                    'subtotal'        => $itemSubtotal,
                ]);
                $subtotal += $itemSubtotal;
            }

            $total = $subtotal + ($data['shipping_cost'] ?? 0);
            $purchase->update(['subtotal' => $subtotal, 'total_amount' => $total, 'due_amount' => $total]);
            return $purchase->fresh();
        });
    }

    public function receive(Purchase $purchase): Purchase
    {
        return DB::transaction(function () use ($purchase) {
            if ($purchase->status === 'received') throw new \Exception('Purchase already received.');
            foreach ($purchase->items as $item) {
                $qty = $item->quantity - $item->received_quantity;
                if ($qty <= 0) continue;
                $this->inventory->increaseStock(
                    $item->item_id, $purchase->warehouse_id, $qty,
                    $item->unit_cost, 'purchase', Purchase::class, $purchase->id,
                    $item->item_variant_id
                );
                $item->update(['received_quantity' => $item->quantity]);
            }
            $purchase->update(['status' => 'received']);
            return $purchase->fresh();
        });
    }

    public function addPayment(Purchase $purchase, array $data): PurchasePayment
    {
        return DB::transaction(function () use ($purchase, $data) {
            $payment = PurchasePayment::create([
                'purchase_id'    => $purchase->id,
                'created_by'     => auth()->id(),
                'amount'         => $data['amount'],
                'payment_method' => $data['payment_method'],
                'reference'      => $data['reference'] ?? null,
                'payment_date'   => $data['payment_date'],
                'notes'          => $data['notes'] ?? null,
            ]);
            $paidAmount = $purchase->payments()->sum('amount');
            $status = $paidAmount >= $purchase->total_amount ? 'paid' : ($paidAmount > 0 ? 'partial' : 'unpaid');
            $purchase->update(['paid_amount' => $paidAmount, 'due_amount' => max(0, $purchase->total_amount - $paidAmount), 'payment_status' => $status]);
            return $payment;
        });
    }

    public function createReturn(Purchase $purchase, array $data): PurchaseReturn
    {
        return DB::transaction(function () use ($purchase, $data) {
            $return = PurchaseReturn::create([
                'reference'    => 'PR-'.strtoupper(Str::random(8)),
                'purchase_id'  => $purchase->id,
                'supplier_id'  => $purchase->supplier_id,
                'warehouse_id' => $purchase->warehouse_id,
                'created_by'   => auth()->id(),
                'return_date'  => $data['return_date'],
                'status'       => 'approved',
                'reason'       => $data['reason'] ?? null,
                'total_amount' => 0,
            ]);
            $total = 0;
            foreach ($data['items'] as $row) {
                $subtotal = $row['quantity'] * $row['unit_cost'];
                PurchaseReturnItem::create([
                    'purchase_return_id' => $return->id,
                    'item_id'            => $row['item_id'],
                    'item_variant_id'    => $row['item_variant_id'] ?? null,
                    'quantity'           => $row['quantity'],
                    'unit_cost'          => $row['unit_cost'],
                    'subtotal'           => $subtotal,
                ]);
                $this->inventory->decreaseStock(
                    $row['item_id'], $purchase->warehouse_id, $row['quantity'],
                    $row['unit_cost'], 'return_out', PurchaseReturn::class, $return->id,
                    $row['item_variant_id'] ?? null
                );
                $total += $subtotal;
            }
            $return->update(['total_amount' => $total]);
            return $return;
        });
    }

    private function generateReference(): string
    {
        return 'PO-'.date('Ymd').'-'.str_pad(Purchase::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
    }
}
