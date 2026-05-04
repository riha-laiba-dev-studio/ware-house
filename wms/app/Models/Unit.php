<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use SoftDeletes;
    protected $fillable = ['name','symbol','description','is_active'];
    protected $casts    = ['is_active'=>'boolean'];
    public function items() { return $this->hasMany(Item::class); }
    public function scopeActive($q) { return $q->where('is_active',true); }
}
