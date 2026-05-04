<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;
    protected $fillable = ['name','code','email','phone','company','address','city','country','opening_balance','is_active','notes'];
    protected $casts    = ['opening_balance'=>'decimal:2','is_active'=>'boolean'];

    public function purchases()       { return $this->hasMany(Purchase::class); }
    public function purchaseReturns() { return $this->hasMany(PurchaseReturn::class); }

    public function getTotalPayableAttribute(): float {
        return $this->purchases()->where('payment_status','!=','paid')->sum('due_amount');
    }
    public function getTotalPaidAttribute(): float {
        return $this->purchases()->sum('paid_amount');
    }
    public function scopeActive($q) { return $q->where('is_active',true); }
}
