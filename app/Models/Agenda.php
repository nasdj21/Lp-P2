<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    use HasFactory;

    protected $table = 'agenda';
    protected $primaryKey = 'id_agenda';

    protected $fillable = [
        'fecha_agenda',
        'id_profesional',
    ];

    protected $casts = [
        'fecha_agenda' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function profesional()
    {
        return $this->belongsTo(Usuario::class, 'id_profesional', 'id');
    }

    public function citas()
    {
        return $this->hasMany(Cita::class, 'id_agenda', 'id_agenda');
    }
}