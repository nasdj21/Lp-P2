<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    //define la talba donde se guarda como service, con PK service:id
    protected $table = 'service';
    protected $primaryKey = 'service_id';
    public $timestamps = false; //Le digo a Laravel que no maneje created_at y updatedat
    
    protected $fillable = [
        'name',
        'price',
        'created_by',
        'modified_by',
        'modification_date'
    ];
    
    protected $casts = [
        'price' => 'decimal:2', //Precio debe tener 2 decimales
    ];
    
    // Relaciones

    /*
    Un servicio puede estar presente en muchas filas de la tabla intermedia professional_service
    */
    public function professionalServices()
    {
        return $this->hasMany(ProfessionalService::class, 'service_id', 'service_id');
    }
    
    /*
    Es una relación Muchos a Muchos. Indica que un servicio es ofrecido por muchos profesionales y un profesional ofrece muchos servicios.
    */
    public function professionals()
    {
        return $this->belongsToMany(Professional::class, 'professional_service', 'service_id', 'person_id', 'service_id', 'person_id');
    }
    
    /*
    Un servicio puede estar asociado a muchos registros de pagos (cada vez que alguien contrata ese servicio).
    */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'service_id', 'service_id');
    }
}
