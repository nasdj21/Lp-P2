<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $table = 'person';
    protected $primaryKey = 'person_id';
    public $timestamps = false;
    
    protected $fillable = [
        'user_account_id',
        'first_name',
        'last_name',
        'birthdate',
        'gender',
        'occupation',
        'marital_status',
        'education',
        'phone',
        'country_id',
        'created_by',
        'modified_by',
        'modification_date'
    ];
    
    // Relaciones
    public function userAccount()
    {
        return $this->belongsTo(UserAccount::class, 'user_account_id', 'user_account_id');
    }
    
    public function genderInfo()
    {
        return $this->belongsTo(Gender::class, 'gender', 'gender_id');
    }
    
    public function occupationInfo()
    {
        return $this->belongsTo(Occupation::class, 'occupation', 'occupation_id');
    }
    
    public function maritalStatusInfo()
    {
        return $this->belongsTo(MaritalStatus::class, 'marital_status', 'marital_status_id');
    }
    
    public function educationInfo()
    {
        return $this->belongsTo(Education::class, 'education', 'education_id');
    }
    
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'country_id');
    }
    
    public function identification()
    {
        return $this->hasOne(Identification::class, 'person_id', 'person_id');
    }
    
    public function client()
    {
        return $this->hasOne(Client::class, 'person_id', 'person_id');
    }
    
    public function professional()
    {
        return $this->hasOne(Professional::class, 'person_id', 'person_id');
    }
    
    public function staff()
    {
        return $this->hasOne(Staff::class, 'person_id', 'person_id');
    }
    
    public function workerSchedules()
    {
        return $this->hasMany(WorkerSchedule::class, 'person_id', 'person_id');
    }
    
    public function scheduledAppointments()
    {
        return $this->hasMany(Appointment::class, 'scheduled_by', 'person_id');
    }
}
