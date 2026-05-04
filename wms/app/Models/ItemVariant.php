<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemVariant extends Model
{
    use SoftDeletes;
    protected $fillable = ['item_id','name','sku','barcode','purchase_price','selling_price','image','is_active'];
    protected $casts    = ['purchase_price'=>'decimal:2','selling_price'=>'decimal:2','is_active'=>'boolean'];
    public function item()      { return $this->belongsTo(Item::class); }
    public function inventory() { return $this->hasMany(Inventory::class); }
}
