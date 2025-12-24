<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ServicioController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $servicios = Servicio::all();
            
            return response()->json([
                'success' => true,
                'message' => 'Servicios obtenidos correctamente',
                'data' => $servicios
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener servicios',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:150',
                'precio' => 'required|numeric|min:0',
            ]);

            $servicio = Servicio::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Servicio creado correctamente',
                'data' => $servicio
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
                'message' => 'Error al crear servicio',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $servicio = Servicio::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Servicio obtenido correctamente',
                'data' => $servicio
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Servicio no encontrado',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}