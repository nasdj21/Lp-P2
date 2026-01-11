<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class UserAccount extends Authenticatable
{
    use HasApiTokens;  // ← IMPORTANTE para tokens
    
    protected $table = 'user_account';
    protected $primaryKey = 'user_account_id';
    public $timestamps = false;
    
    protected $fillable = [
        'role_id',
        'email',
        'password_hash',
        'status',
        'last_login',
        'created_by',
        'modified_by',
        'modification_date'
    ];
    
    protected $hidden = [
        'password_hash',
    ];
    
    // IMPORTANTE: Laravel espera que el campo se llame 'password'
    // Pero nosotros usamos 'password_hash', así que lo mapeamos
    public function getAuthPassword()
    {
        return $this->password_hash;
    }
    
    // Relaciones
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }
    
    public function accountStatus()
    {
        return $this->belongsTo(UserAccountStatus::class, 'status', 'status_id');
    }
    
    public function person()
    {
        return $this->hasOne(Person::class, 'user_account_id', 'user_account_id');
    }
}