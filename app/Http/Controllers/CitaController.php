<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use Illuminate\Http\Request;

class CitaController extends Controller
{
public function index()
    {
        // Recuperar todas las citas de la base de datos
        $citas = Cita::all();

        // Devolver una vista o JSON con las citas
        return view('citas.index', compact('citas'));
    }
}
