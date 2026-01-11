<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentStatus extends Model
{
    protected $table = 'payment_status';
    protected $primaryKey = 'status_id';
    public $timestamps = false;
    
    protected $fillable = [
        'name'
    ];
    
    // Relaciones
    public function payments()
    {
        return $this->hasMany(Payment::class, 'status_id', 'status_id');
    }
}
