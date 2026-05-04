<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Brand extends Model
{
    use SoftDeletes;
    protected $fillable = ['name','slug','logo','description','is_active'];
    protected $casts    = ['is_active'=>'boolean'];

    protected static function boot() {
        parent::boot();
        static::creating(fn($m) => $m->slug = $m->slug ?: Str::slug($m->name));
    }
    public function items() { return $this->hasMany(Item::class); }
    public function scopeActive($q) { return $q->where('is_active',true); }
}
