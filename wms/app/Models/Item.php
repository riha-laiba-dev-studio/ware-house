<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Item extends Model
{
    use SoftDeletes;
    protected $fillable = ['name','sku','barcode','category_id','unit_id','brand_id','description','purchase_price','selling_price','min_selling_price','alert_quantity','image','has_variants','is_active'];
    protected $casts    = ['purchase_price'=>'decimal:2','selling_price'=>'decimal:2','min_selling_price'=>'decimal:2','has_variants'=>'boolean','is_active'=>'boolean'];

    protected static function boot() {
        parent::boot();
        static::creating(function($m) {
            if (!$m->sku) $m->sku = 'SKU-'.strtoupper(Str::random(8));
        });
    }

    public function category()   { return $this->belongsTo(Category::class); }
    public function unit()       { return $this->belongsTo(Unit::class); }
    public function brand()      { return $this->belongsTo(Brand::class); }
    public function variants()   { return $this->hasMany(ItemVariant::class); }
    public function inventory()  { return $this->hasMany(Inventory::class); }
    public function movements()  { return $this->hasMany(InventoryMovement::class); }

    public function getTotalStockAttribute(): float {
        return $this->inventory()->sum('quantity');
    }
    public function getStockForWarehouse(int $warehouseId): float {
        return $this->inventory()->where('warehouse_id',$warehouseId)->value('quantity') ?? 0;
    }
    public function isLowStock(): bool {
        return $this->getTotalStockAttribute() <= $this->alert_quantity;
    }
    public function getImageUrlAttribute(): string {
        return $this->image ? asset('storage/'.$this->image) : asset('images/no-image.png');
    }
    public function scopeActive($q) { return $q->where('is_active',true); }
    public function scopeLowStock($q) { return $q->whereHas('inventory',fn($i) => $i->whereRaw('quantity <= items.alert_quantity')); }
}
