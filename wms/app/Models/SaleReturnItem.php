<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleReturnItem extends Model
{
    protected $fillable = ['sale_return_id','item_id','item_variant_id','quantity','unit_price','subtotal'];
    protected $casts    = ['quantity'=>'decimal:4','unit_price'=>'decimal:2','subtotal'=>'decimal:2'];
    public function saleReturn(){ return $this->belongsTo(SaleReturn::class); }
    public function item()      { return $this->belongsTo(Item::class); }
    public function variant()   { return $this->belongsTo(ItemVariant::class,'item_variant_id'); }
}
