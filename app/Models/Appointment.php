<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $table = 'appointment';
    protected $primaryKey = 'appointment_id';
    public $timestamps = false;
    
    protected $fillable = [
        'payment_id',
        'scheduled_by',
        'worker_schedule_id',
        'status',
        'created_by',
        'modified_by',
        'modification_date'
    ];
    
    // Relaciones
    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id', 'payment_id');
    }
    
    public function scheduledByPerson()
    {
        return $this->belongsTo(Person::class, 'scheduled_by', 'person_id');
    }
    
    public function workerSchedule()
    {
        return $this->belongsTo(WorkerSchedule::class, 'worker_schedule_id', 'worker_schedule_id');
    }
    
    public function appointmentStatus()
    {
        return $this->belongsTo(AppointmentStatus::class, 'status', 'status_id');
    }
}
