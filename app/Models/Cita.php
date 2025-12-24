<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
public function paciente()
    {
        return $this->belongsTo(Usuario::class, 'paciente_id');
    }

    public function terapeuta()
    {
        return $this->belongsTo(Usuario::class, 'terapeuta_id');
    }

    public function reporte()
    {
        return $this->hasOne(Reporte::class);
    }

    public function pago()
    {
        return $this->hasOne(Pago::class);
    }
}
