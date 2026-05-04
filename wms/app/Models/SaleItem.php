<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $fillable = ['sale_id','item_id','item_variant_id','quantity','unit_price','discount_percent','discount_amount','tax_percent','tax_amount','subtotal','purchase_price'];
    protected $casts    = ['quantity'=>'decimal:4','unit_price'=>'decimal:2','discount_percent'=>'decimal:2','discount_amount'=>'decimal:2','tax_percent'=>'decimal:2','tax_amount'=>'decimal:2','subtotal'=>'decimal:2','purchase_price'=>'decimal:2'];
    public function sale()    { return $this->belongsTo(Sale::class); }
    public function item()    { return $this->belongsTo(Item::class); }
    public function variant() { return $this->belongsTo(ItemVariant::class,'item_variant_id'); }
    public function getProfitAttribute() { return ($this->unit_price - $this->purchase_price) * $this->quantity; }
}
