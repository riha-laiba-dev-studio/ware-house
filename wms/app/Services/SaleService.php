<?php
namespace App\Services;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SaleService
{
    public function __construct(private InventoryService $inventory) {}

    public function create(array $data): Sale
    {
        return DB::transaction(function () use ($data) {
            $sale = Sale::create([
                'reference'      => $this->generateReference(),
                'customer_id'    => $data['customer_id'],
                'warehouse_id'   => $data['warehouse_id'],
                'created_by'     => auth()->id(),
                'sale_date'      => $data['sale_date'],
                'status'         => 'confirmed',
                'notes'          => $data['notes'] ?? null,
                'subtotal'       => 0, 'discount_amount' => $data['discount_amount'] ?? 0,
                'tax_amount'     => $data['tax_amount'] ?? 0,
                'shipping_cost'  => $data['shipping_cost'] ?? 0,
                'total_amount'   => 0, 'paid_amount' => 0, 'due_amount' => 0,
                'payment_status' => 'unpaid',
            ]);
            $subtotal = 0;
            foreach ($data['items'] as $row) {
                $this->inventory->decreaseStock(
                    $row['item_id'], $data['warehouse_id'], $row['quantity'],
                    $row['purchase_price'] ?? 0, 'sale', Sale::class, $sale->id,
                    $row['item_variant_id'] ?? null
                );
                $itemSubtotal = ($row['quantity'] * $row['unit_price']) - ($row['discount_amount'] ?? 0) + ($row['tax_amount'] ?? 0);
                SaleItem::create([
                    'sale_id'         => $sale->id,
                    'item_id'         => $row['item_id'],
                    'item_variant_id' => $row['item_variant_id'] ?? null,
                    'quantity'        => $row['quantity'],
                    'unit_price'      => $row['unit_price'],
                    'discount_percent'=> $row['discount_percent'] ?? 0,
                    'discount_amount' => $row['discount_amount'] ?? 0,
                    'tax_percent'     => $row['tax_percent'] ?? 0,
                    'tax_amount'      => $row['tax_amount'] ?? 0,
                    'subtotal'        => $itemSubtotal,
                    'purchase_price'  => $row['purchase_price'] ?? 0,
                ]);
                $subtotal += $itemSubtotal;
            }
            $total = $subtotal - ($data['discount_amount'] ?? 0) + ($data['tax_amount'] ?? 0) + ($data['shipping_cost'] ?? 0);
            $paidNow = $data['paid_amount'] ?? 0;
            $payStatus = $paidNow >= $total ? 'paid' : ($paidNow > 0 ? 'partial' : 'unpaid');
            $sale->update(['subtotal' => $subtotal, 'total_amount' => $total, 'paid_amount' => $paidNow, 'due_amount' => max(0,$total - $paidNow), 'payment_status' => $payStatus]);
            if ($paidNow > 0) {
                SalePayment::create(['sale_id'=>$sale->id,'created_by'=>auth()->id(),'amount'=>$paidNow,'payment_method'=>$data['payment_method']??'cash','payment_date'=>$data['sale_date']]);
            }
            return $sale->fresh();
        });
    }

    public function addPayment(Sale $sale, array $data): SalePayment
    {
        return DB::transaction(function () use ($sale, $data) {
            $payment    = SalePayment::create(['sale_id'=>$sale->id,'created_by'=>auth()->id(),'amount'=>$data['amount'],'payment_method'=>$data['payment_method'],'reference'=>$data['reference']??null,'payment_date'=>$data['payment_date'],'notes'=>$data['notes']??null]);
            $paidAmount = $sale->payments()->sum('amount');
            $status     = $paidAmount >= $sale->total_amount ? 'paid' : ($paidAmount > 0 ? 'partial' : 'unpaid');
            $sale->update(['paid_amount'=>$paidAmount,'due_amount'=>max(0,$sale->total_amount-$paidAmount),'payment_status'=>$status]);
            return $payment;
        });
    }

    public function createReturn(Sale $sale, array $data): SaleReturn
    {
        return DB::transaction(function () use ($sale, $data) {
            $return = SaleReturn::create(['reference'=>'SR-'.strtoupper(Str::random(8)),'sale_id'=>$sale->id,'customer_id'=>$sale->customer_id,'warehouse_id'=>$sale->warehouse_id,'created_by'=>auth()->id(),'return_date'=>$data['return_date'],'status'=>'approved','reason'=>$data['reason']??null,'total_amount'=>0]);
            $total = 0;
            foreach ($data['items'] as $row) {
                $subtotal = $row['quantity'] * $row['unit_price'];
                SaleReturnItem::create(['sale_return_id'=>$return->id,'item_id'=>$row['item_id'],'item_variant_id'=>$row['item_variant_id']??null,'quantity'=>$row['quantity'],'unit_price'=>$row['unit_price'],'subtotal'=>$subtotal]);
                $this->inventory->increaseStock($row['item_id'],$sale->warehouse_id,$row['quantity'],$row['unit_price']??0,'return_in',SaleReturn::class,$return->id,$row['item_variant_id']??null);
                $total += $subtotal;
            }
            $return->update(['total_amount'=>$total]);
            return $return;
        });
    }

    private function generateReference(): string
    {
        return 'SO-'.date('Ymd').'-'.str_pad(Sale::whereDate('created_at',today())->count()+1,4,'0',STR_PAD_LEFT);
    }
}
