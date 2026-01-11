<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Occupation extends Model
{
    protected $table = 'occupation';
    protected $primaryKey = 'occupation_id';
    public $timestamps = false;
    
    protected $fillable = [
        'name'
    ];
    
    // Relaciones
    public function persons()
    {
        return $this->hasMany(Person::class, 'occupation', 'occupation_id');
    }
}
