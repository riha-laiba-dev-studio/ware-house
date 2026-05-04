<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturnItem extends Model
{
    protected $fillable = ['purchase_return_id','item_id','item_variant_id','quantity','unit_cost','subtotal'];
    protected $casts    = ['quantity'=>'decimal:4','unit_cost'=>'decimal:2','subtotal'=>'decimal:2'];
    public function purchaseReturn() { return $this->belongsTo(PurchaseReturn::class); }
    public function item()   { return $this->belongsTo(Item::class); }
    public function variant(){ return $this->belongsTo(ItemVariant::class,'item_variant_id'); }
}
