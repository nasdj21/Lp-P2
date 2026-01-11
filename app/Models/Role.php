<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{    
    protected $table = 'role';
    protected $primaryKey = 'role_id';
    public $timestamps = false;
    
    protected $fillable = [
        'name',
        'created_by',
        'modified_by',
        'modification_date'
    ];
    
    // Relaciones
    public function userAccounts()
    {
        return $this->hasMany(UserAccount::class, 'role_id', 'role_id');
    }
}
