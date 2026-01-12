<?php

namespace App\Http\Controllers;
use App\Models\Service;
use App\Models\Professional;

use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Mostrar un listado de servicios con sus profesionales y recuento de pagos (GET /api/services)
     */
    public function index()
    {
        // Cargar servicios con profesionales (y sus personas/usuarios) y pagos
        $services = Service::with([
            'professionals.person.userAccount',
            'payments'
        ])->get();

        // Transformar colección a estructura descriptiva
        return response()->json($services->map(function($service){
            return [
                'service_id' => $service->service_id,
                'name' => $service->name,
                'price' => $service->price,
                'professionals' => $service->professionals->map(function($professional){
                   return [
                    'person_id' => $professional->person_id,
                    'first_name' => $professional->person->first_name ?? null,
                    'last_name' => $professional->person->last_name ?? null,
                    'email' => $professional->person->userAccount->email ?? null,
                    'title' => $professional->title ?? null,
                    'specialty' => $professional->specialty ?? null,
                   ]; 
                }),
                'payments_count' => $service->payments->count(),
                'created_by' => $service->created_by,
                'creation_date' => $service->creation_date,
                'modified_by' => $service->modified_by,
                'modification_date' => $service->modification_date,
            ];
        }));
    }

    /**
     * Crear un nuevo servicio (POST /api/services)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:service,name',
            'price' => 'required|numeric|get:0',
            'professionals' => 'sometimes|array',
            'professionals.*' => 'exists:professional,person_id',
            'created_by' => 'nullable|string|max:255',
        ]);

        //Extraer profesionales y eliminar del array validado
        $professionalIds = $validated['professionals'] ?? [];
        unset($validated['professionals']);

        //crear servicio
        $service = Service::create($valited);

        // Asociar profesionales is es que se enviaron
        if (!empty($professionalIds)){
            $service->professionals()->attach($professionalIds);
        }

        return response()->jsoon([
            'message' => 'Service created successfully',
            'service' => $service->load(['professioanls.person.userAccount'])
        ], 201);
    }

    /**
     * Mostrar un servicio particular (GET /api/services/{id})
     */
    public function show(string $id)
    {
        $service = Service::with([
            'professionals.person.userAccount',
            'payments'
        ])->findOrFail($id);

        return response()->json([
            'service_id'       => $service->service_id,
            'name'             => $service->name,
            'price'            => $service->price,
            'professionals'    => $service->professionals->map(function ($professional) {
                return [
                    'person_id'  => $professional->person_id,
                    'first_name' => $professional->person->first_name ?? null,
                    'last_name'  => $professional->person->last_name ?? null,
                    'email'      => $professional->person->userAccount->email ?? null,
                    'title'      => $professional->title ?? null,
                    'specialty'  => $professional->specialty ?? null,
                ];
            }),
            'payments_count'   => $service->payments->count(),
            'created_by'       => $service->created_by,
            'creation_date'    => $service->creation_date,
            'modified_by'      => $service->modified_by,
            'modification_date'=> $service->modification_date,
        ]);
    }

    /**
     * Actualiza la data de un servicio (PUT/PATCH /api/services/{id})
     */
    public function update(Request $request, string $id)
    {
        $service = Service::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255|unique:service,name,' . $service->service_id . ',service_id',
            'prince' => 'sometimes|numeric|gte:0',
            'professionals' => 'sometimes|array',
            'professionals.*' => 'exists:professional, person_id',
            'modified_by' => 'nullable|string|max:255',
        ]);

        //Extrae la lista de profesionales
        $professionalIds = $validated['professionals'] ?? null;
        unset($validated['professionals']);

        //Si se ha modificado, modifica fecha de modificación
        if (!empty($validated)){
            $validated['modification_date'] = now();
        }

        $service->update($validated);

        //Actualizar asociaciones de profesionales
        if (is_array($professionalIds)){
            $service->professionals()->sync($professionalIds);
        }

        return response()->json([
            'message' => 'Servicio actualizado correctamente',
            'service' => $service->load(['professionals.person.userAccount'])
        ]);
    }

    /**
     * Eliminar un servicio.(DELETE /api/services/{id})
     */
    public function destroy(string $id)
    {
        $service = Service::findOrFail($id);
        $service->delete();

        return response()->json([
            'message' => 'Service deleted successfully'
        ]);
    }
}
