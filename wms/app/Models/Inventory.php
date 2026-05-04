<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table    = 'inventory';
    protected $fillable = ['item_id','item_variant_id','warehouse_id','quantity','reserved_quantity'];
    protected $casts    = ['quantity'=>'decimal:4','reserved_quantity'=>'decimal:4'];

    public function item()      { return $this->belongsTo(Item::class); }
    public function variant()   { return $this->belongsTo(ItemVariant::class,'item_variant_id'); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }

    public function getAvailableQuantityAttribute(): float {
        return max(0, (float)$this->quantity - (float)$this->reserved_quantity);
    }

    public function getStockValueAttribute(): float {
        return (float)$this->quantity * (float)($this->item?->purchase_price ?? 0);
    }
}
