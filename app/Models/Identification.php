<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Identification extends Model
{
    protected $table = 'identification';
    protected $primaryKey = 'identification_id';
    public $timestamps = false;
    
    protected $fillable = [
        'person_id',
        'number',
        'created_by',
        'modified_by',
        'modification_date'
    ];
    
    // Relaciones
    public function person()
    {
        return $this->belongsTo(Person::class, 'person_id', 'person_id');
    }
}
