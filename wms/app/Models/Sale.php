<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use SoftDeletes;
    protected $fillable = ['reference','customer_id','warehouse_id','created_by','sale_date','status','subtotal','discount_amount','tax_amount','shipping_cost','total_amount','paid_amount','due_amount','payment_status','notes'];
    protected $casts    = ['sale_date'=>'date','subtotal'=>'decimal:2','discount_amount'=>'decimal:2','tax_amount'=>'decimal:2','shipping_cost'=>'decimal:2','total_amount'=>'decimal:2','paid_amount'=>'decimal:2','due_amount'=>'decimal:2'];

    public function customer()  { return $this->belongsTo(Customer::class); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function creator()   { return $this->belongsTo(User::class,'created_by'); }
    public function items()     { return $this->hasMany(SaleItem::class); }
    public function payments()  { return $this->hasMany(SalePayment::class); }
    public function returns()   { return $this->hasMany(SaleReturn::class); }

    public function scopeStatus($q,$s)    { return $q->where('status',$s); }
    public function scopeDateRange($q,$f,$t){ return $q->whereBetween('sale_date',[$f,$t]); }
}
