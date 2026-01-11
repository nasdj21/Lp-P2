<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payment';
    protected $primaryKey = 'payment_id';
    public $timestamps = false;
    
    protected $fillable = [
        'person_id',
        'service_id',
        'status_id',
        'file',
        'created_by',
        'modified_by',
        'modification_date'
    ];
    
    // Relaciones
    public function client()
    {
        return $this->belongsTo(Client::class, 'person_id', 'person_id');
    }
    
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'service_id');
    }
    
    public function paymentStatus()
    {
        return $this->belongsTo(PaymentStatus::class, 'status_id', 'status_id');
    }
    
    public function receipt()
    {
        return $this->hasOne(Receipt::class, 'payment_id', 'payment_id');
    }
    
    public function appointment()
    {
        return $this->hasOne(Appointment::class, 'payment_id', 'payment_id');
    }
}
