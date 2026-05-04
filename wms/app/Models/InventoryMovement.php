<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    protected $fillable = ['item_id','item_variant_id','warehouse_id','type','quantity','before_quantity','after_quantity','unit_cost','reference_type','reference_id','created_by','notes','movement_date'];
    protected $casts    = ['quantity'=>'decimal:4','before_quantity'=>'decimal:4','after_quantity'=>'decimal:4','unit_cost'=>'decimal:2','movement_date'=>'datetime'];

    public function item()      { return $this->belongsTo(Item::class); }
    public function variant()   { return $this->belongsTo(ItemVariant::class,'item_variant_id'); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function creator()   { return $this->belongsTo(User::class,'created_by'); }
    public function reference() { return $this->morphTo(); }

    public function scopeOfType($q,$type)     { return $q->where('type',$type); }
    public function scopeForItem($q,$itemId)  { return $q->where('item_id',$itemId); }
    public function scopeForWarehouse($q,$wId){ return $q->where('warehouse_id',$wId); }
    public function scopeDateRange($q,$from,$to) { return $q->whereBetween('movement_date',[$from,$to]); }
}
