<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CitaController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $citas = Cita::with(['paciente', 'profesional', 'agenda', 'servicio'])->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Citas obtenidas correctamente',
                'data' => $citas
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener citas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'id_paciente' => 'required|integer|exists:usuarios,id',
                'id_profesional' => 'required|integer|exists:usuarios,id',
                'id_agenda' => 'required|integer|exists:agenda,id_agenda',
                'id_servicio' => 'required|integer|exists:servicios,id_servicio',
            ]);

            $cita = Cita::create($validated);
            $cita->load(['paciente', 'profesional', 'agenda', 'servicio']);

            return response()->json([
                'success' => true,
                'message' => 'Cita creada correctamente',
                'data' => $cita
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validacion',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear cita',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $cita = Cita::with(['paciente', 'profesional', 'agenda', 'servicio'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Cita obtenida correctamente',
                'data' => $cita
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cita no encontrada',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}