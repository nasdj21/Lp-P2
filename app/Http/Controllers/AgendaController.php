<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AgendaController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $agendas = Agenda::with('profesional')->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Agendas obtenidas correctamente',
                'data' => $agendas
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener agendas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'fecha_agenda' => 'required|date',
                'id_profesional' => 'required|integer|exists:usuarios,id',
            ]);

            $agenda = Agenda::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Agenda creada correctamente',
                'data' => $agenda
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
                'message' => 'Error al crear agenda',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $agenda = Agenda::with('profesional')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Agenda obtenida correctamente',
                'data' => $agenda
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Agenda no encontrada',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}