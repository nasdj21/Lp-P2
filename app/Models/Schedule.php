<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $table = 'schedule';
    protected $primaryKey = 'schedule_id';
    public $timestamps = false;
    
    protected $fillable = [
        'date',
        'start_time',
        'end_time',
        'created_by',
        'modified_by',
        'modification_date'
    ];
    
    // Relaciones
    public function workerSchedules()
    {
        return $this->hasMany(WorkerSchedule::class, 'schedule_id', 'schedule_id');
    }
}
