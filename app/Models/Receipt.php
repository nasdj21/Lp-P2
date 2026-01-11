<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    protected $table = 'receipt';
    protected $primaryKey = 'receipt_id';
    public $timestamps = false;
    
    protected $fillable = [
        'payment_id',
        'created_by',
        'modified_by',
        'modification_date'
    ];
    
    // Relaciones
    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id', 'payment_id');
    }
}
