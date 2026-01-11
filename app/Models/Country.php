<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'country';
    protected $primaryKey = 'country_id';
    public $timestamps = false;
    
    protected $fillable = [
        'name',
        'phone_code'
    ];
    
    // Relaciones
    public function states()
    {
        return $this->hasMany(State::class, 'country_id', 'country_id');
    }
    
    public function persons()
    {
        return $this->hasMany(Person::class, 'country_id', 'country_id');
    }
}
