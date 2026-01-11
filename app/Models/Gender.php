<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gender extends Model
{
    protected $table = 'gender';
    protected $primaryKey = 'gender_id';
    public $timestamps = false;
    
    protected $fillable = [
        'name'
    ];
    
    // Relaciones
    public function persons()
    {
        return $this->hasMany(Person::class, 'gender', 'gender_id');
    }
}
