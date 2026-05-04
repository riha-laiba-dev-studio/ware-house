<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchasePayment extends Model
{
    protected $fillable = ['purchase_id','created_by','amount','payment_method','reference','payment_date','notes'];
    protected $casts    = ['amount'=>'decimal:2','payment_date'=>'date'];
    public function purchase() { return $this->belongsTo(Purchase::class); }
    public function creator()  { return $this->belongsTo(User::class,'created_by'); }
}
