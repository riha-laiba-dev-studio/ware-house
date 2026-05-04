<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalePayment extends Model
{
    protected $fillable = ['sale_id','created_by','amount','payment_method','reference','payment_date','notes'];
    protected $casts    = ['amount'=>'decimal:2','payment_date'=>'date'];
    public function sale()    { return $this->belongsTo(Sale::class); }
    public function creator() { return $this->belongsTo(User::class,'created_by'); }
}
