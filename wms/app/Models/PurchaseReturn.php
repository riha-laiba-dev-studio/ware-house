<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseReturn extends Model
{
    use SoftDeletes;
    protected $fillable = ['reference','purchase_id','supplier_id','warehouse_id','created_by','return_date','status','total_amount','reason'];
    protected $casts    = ['return_date'=>'date','total_amount'=>'decimal:2'];
    public function purchase()  { return $this->belongsTo(Purchase::class); }
    public function supplier()  { return $this->belongsTo(Supplier::class); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function creator()   { return $this->belongsTo(User::class,'created_by'); }
    public function items()     { return $this->hasMany(PurchaseReturnItem::class); }
}
