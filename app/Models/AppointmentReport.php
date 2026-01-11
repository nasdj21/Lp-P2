<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentReport extends Model
{
    protected $table = 'appointment_report';
    protected $primaryKey = 'appointment_report_id';
    public $timestamps = false;
    
    protected $fillable = [
        'appointment_id',
        'file',
        'created_by',
        'modified_by',
        'modification_date'
    ];
    
    // Relaciones
    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id', 'appointment_id');
    }
}
