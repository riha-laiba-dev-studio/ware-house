<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockTransfer extends Model
{
    use SoftDeletes;
    protected $fillable = ['reference','from_warehouse_id','to_warehouse_id','created_by','transfer_date','status','notes'];
    protected $casts    = ['transfer_date'=>'date'];
    public function fromWarehouse() { return $this->belongsTo(Warehouse::class,'from_warehouse_id'); }
    public function toWarehouse()   { return $this->belongsTo(Warehouse::class,'to_warehouse_id'); }
    public function creator()       { return $this->belongsTo(User::class,'created_by'); }
    public function items()         { return $this->hasMany(StockTransferItem::class); }
}
