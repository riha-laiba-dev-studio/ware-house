<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;
    protected $fillable = ['name','code','email','phone','company','address','city','country','opening_balance','credit_limit','is_active','notes'];
    protected $casts    = ['opening_balance'=>'decimal:2','credit_limit'=>'decimal:2','is_active'=>'boolean'];

    public function sales()       { return $this->hasMany(Sale::class); }
    public function saleReturns() { return $this->hasMany(SaleReturn::class); }

    public function getTotalReceivableAttribute(): float {
        return $this->sales()->where('payment_status','!=','paid')->sum('due_amount');
    }
    public function getTotalPaidAttribute(): float {
        return $this->sales()->sum('paid_amount');
    }
    public function scopeActive($q) { return $q->where('is_active',true); }
}
