<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = ['name','email','phone','avatar','password','is_active','last_login_at','last_login_ip'];
    protected $hidden   = ['password','remember_token'];
    protected $casts    = ['email_verified_at'=>'datetime','last_login_at'=>'datetime','is_active'=>'boolean','password'=>'hashed'];

    public function activityLogs() { return $this->hasMany(ActivityLog::class); }
    public function loginLogs()    { return $this->hasMany(LoginLog::class); }
    public function purchases()    { return $this->hasMany(Purchase::class,'created_by'); }
    public function sales()        { return $this->hasMany(Sale::class,'created_by'); }
    public function managedWarehouses() { return $this->hasMany(Warehouse::class,'manager_id'); }
    public function getAvatarUrlAttribute(): string {
        return $this->avatar ? asset('storage/'.$this->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&background=2563eb&color=fff';
    }
    public function scopeActive($query) { return $query->where('is_active',true); }
}
