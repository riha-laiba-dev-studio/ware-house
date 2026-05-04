<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use SoftDeletes;
    protected $fillable = ['name','code','address','city','phone','email','manager_id','is_active','notes'];
    protected $casts    = ['is_active'=>'boolean'];

    public function manager()    { return $this->belongsTo(User::class,'manager_id'); }
    public function inventory()  { return $this->hasMany(Inventory::class); }
    public function purchases()  { return $this->hasMany(Purchase::class); }
    public function sales()      { return $this->hasMany(Sale::class); }
    public function transfersFrom() { return $this->hasMany(StockTransfer::class,'from_warehouse_id'); }
    public function transfersTo()   { return $this->hasMany(StockTransfer::class,'to_warehouse_id'); }
    public function adjustments()   { return $this->hasMany(InventoryAdjustment::class); }
    public function movements()     { return $this->hasMany(InventoryMovement::class); }
    public function scopeActive($q) { return $q->where('is_active',true); }
}
