<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $fillable = ['purchase_id','item_id','item_variant_id','quantity','received_quantity','unit_cost','discount_percent','discount_amount','tax_percent','tax_amount','subtotal'];
    protected $casts    = ['quantity'=>'decimal:4','received_quantity'=>'decimal:4','unit_cost'=>'decimal:2','discount_percent'=>'decimal:2','discount_amount'=>'decimal:2','tax_percent'=>'decimal:2','tax_amount'=>'decimal:2','subtotal'=>'decimal:2'];
    public function purchase() { return $this->belongsTo(Purchase::class); }
    public function item()     { return $this->belongsTo(Item::class); }
    public function variant()  { return $this->belongsTo(ItemVariant::class,'item_variant_id'); }
}
