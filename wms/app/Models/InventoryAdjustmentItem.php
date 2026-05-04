<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryAdjustmentItem extends Model
{
    protected $fillable = ['inventory_adjustment_id','item_id','item_variant_id','current_quantity','adjusted_quantity','difference','unit_cost','reason'];
    protected $casts    = ['current_quantity'=>'decimal:4','adjusted_quantity'=>'decimal:4','difference'=>'decimal:4','unit_cost'=>'decimal:2'];
    public function adjustment(){ return $this->belongsTo(InventoryAdjustment::class,'inventory_adjustment_id'); }
    public function item()      { return $this->belongsTo(Item::class); }
    public function variant()   { return $this->belongsTo(ItemVariant::class,'item_variant_id'); }
}
