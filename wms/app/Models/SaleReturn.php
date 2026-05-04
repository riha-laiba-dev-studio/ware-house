<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleReturn extends Model
{
    use SoftDeletes;
    protected $fillable = ['reference','sale_id','customer_id','warehouse_id','created_by','return_date','status','total_amount','reason'];
    protected $casts    = ['return_date'=>'date','total_amount'=>'decimal:2'];
    public function sale()      { return $this->belongsTo(Sale::class); }
    public function customer()  { return $this->belongsTo(Customer::class); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function creator()   { return $this->belongsTo(User::class,'created_by'); }
    public function items()     { return $this->hasMany(SaleReturnItem::class); }
}
