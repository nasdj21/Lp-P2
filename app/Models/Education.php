<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    protected $table = 'education';
    protected $primaryKey = 'education_id';
    public $timestamps = false;
    
    protected $fillable = [
        'name'
    ];
    
    // Relaciones
    public function persons()
    {
        return $this->hasMany(Person::class, 'education', 'education_id');
    }
}
