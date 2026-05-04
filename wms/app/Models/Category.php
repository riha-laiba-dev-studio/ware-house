<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Category extends Model
{
    use SoftDeletes;
    protected $fillable = ['name','slug','parent_id','description','is_active'];
    protected $casts    = ['is_active'=>'boolean'];

    protected static function boot() {
        parent::boot();
        static::creating(fn($m) => $m->slug = $m->slug ?: Str::slug($m->name));
    }

    public function parent()   { return $this->belongsTo(Category::class,'parent_id'); }
    public function children() { return $this->hasMany(Category::class,'parent_id'); }
    public function items()    { return $this->hasMany(Item::class); }
    public function scopeActive($q) { return $q->where('is_active',true); }
}
