<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProfessionalService;
use App\Models\Service;
use App\Models\Professional;

class ProfessionalServiceController extends Controller
{
    /**
     * Display a listing of professional services with full details.
     * GET /api/professional-services
     */
    public function index()
    {
        $professionalServices = ProfessionalService::with([
            'service',
            'professional.person.userAccount',
            'professional.person.identification',
            'professional.person.genderInfo',
        ])->get();

        return response()->json($professionalServices->map(function ($ps) {
            return [
                'professional_service_id' => $ps->professional_service_id,
                'service' => [
                    'service_id' => $ps->service->service_id,
                    'name' => $ps->service->name,
                    'price' => $ps->service->price,
                ],
                'professional' => [
                    'person_id' => $ps->professional->person_id,
                    'first_name' => $ps->professional->person->first_name,
                    'last_name' => $ps->professional->person->last_name,
                    'email' => $ps->professional->person->userAccount->email ?? null,
                    'phone' => $ps->professional->person->phone ?? null,
                    'specialty' => $ps->professional->specialty,
                    'title' => $ps->professional->title,
                    'identification' => $ps->professional->person->identification->number ?? null,
                    'gender' => $ps->professional->person->genderInfo->name ?? null,
                ],
                'created_by' => $ps->created_by,
                'creation_date' => $ps->creation_date,
                'modified_by' => $ps->modified_by,
                'modification_date' => $ps->modification_date,
            ];
        }));
    }

    /**
     * Store a newly created professional service (asignar servicio a profesional).
     * POST /api/professional-services
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:service,service_id',
            'person_id' => 'required|exists:professional,person_id',
            'created_by' => 'nullable|string|max:255',
        ]);

        // Verificar que no exista ya la relación
        $exists = ProfessionalService::where('service_id', $validated['service_id'])
            ->where('person_id', $validated['person_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Este profesional ya tiene asignado este servicio'
            ], 409);
        }

        $professionalService = ProfessionalService::create($validated);

        return response()->json([
            'message' => 'Professional service created successfully',
            'professional_service' => $professionalService->load('service', 'professional.person')
        ], 201);
    }

    /**
     * Display the specified professional service.
     * GET /api/professional-services/{id}
     */
    public function show(string $id)
    {
        $ps = ProfessionalService::with([
            'service',
            'professional.person.userAccount',
            'professional.person.identification',
            'professional.person.genderInfo',
        ])->findOrFail($id);

        return response()->json([
            'professional_service_id' => $ps->professional_service_id,
            'service' => [
                'service_id' => $ps->service->service_id,
                'name' => $ps->service->name,
                'price' => $ps->service->price,
            ],
            'professional' => [
                'person_id' => $ps->professional->person_id,
                'first_name' => $ps->professional->person->first_name,
                'last_name' => $ps->professional->person->last_name,
                'email' => $ps->professional->person->userAccount->email ?? null,
                'phone' => $ps->professional->person->phone ?? null,
                'specialty' => $ps->professional->specialty,
                'title' => $ps->professional->title,
                'identification' => $ps->professional->person->identification->number ?? null,
                'gender' => $ps->professional->person->genderInfo->name ?? null,
            ],
            'created_by' => $ps->created_by,
            'creation_date' => $ps->creation_date,
            'modified_by' => $ps->modified_by,
            'modification_date' => $ps->modification_date,
        ]);
    }

    /**
     * Update the specified professional service.
     * PUT/PATCH /api/professional-services/{id}
     */
    public function update(Request $request, string $id)
    {
        $professionalService = ProfessionalService::findOrFail($id);

        $validated = $request->validate([
            'service_id' => 'sometimes|exists:service,service_id',
            'person_id' => 'sometimes|exists:professional,person_id',
            'modified_by' => 'nullable|string|max:255',
        ]);

        // Verificar que no exista ya la relación si se está cambiando
        if (isset($validated['service_id']) || isset($validated['person_id'])) {
            $serviceId = $validated['service_id'] ?? $professionalService->service_id;
            $personId = $validated['person_id'] ?? $professionalService->person_id;

            $exists = ProfessionalService::where('service_id', $serviceId)
                ->where('person_id', $personId)
                ->where('professional_service_id', '!=', $id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'message' => 'Esta combinación de profesional y servicio ya existe'
                ], 409);
            }
        }

        $validated['modification_date'] = now();
        $professionalService->update($validated);

        return response()->json([
            'message' => 'Professional service updated successfully',
            'professional_service' => $professionalService->load('service', 'professional.person')
        ]);
    }

    /**
     * Remove the specified professional service.
     * DELETE /api/professional-services/{id}
     */
    public function destroy(string $id)
    {
        $professionalService = ProfessionalService::findOrFail($id);
        $professionalService->delete();

        return response()->json([
            'message' => 'Professional service deleted successfully'
        ]);
    }

    /**
     * Get services by professional.
     * GET /api/professional-services/professional/{professionalId}
     */
    public function getByProfessional(string $professionalId)
    {
        $professionalServices = ProfessionalService::with('service')
            ->where('person_id', $professionalId)
            ->get();

        return response()->json($professionalServices->map(function ($ps) {
            return [
                'professional_service_id' => $ps->professional_service_id,
                'service_id' => $ps->service->service_id,
                'name' => $ps->service->name,
                'price' => $ps->service->price,
            ];
        }));
    }

    /**
     * Get professionals by service.
     * GET /api/professional-services/service/{serviceId}
     */
    public function getByService(string $serviceId)
    {
        $professionalServices = ProfessionalService::with([
            'professional.person.userAccount',
            'professional.person.identification',
        ])->where('service_id', $serviceId)
            ->get();

        return response()->json($professionalServices->map(function ($ps) {
            return [
                'professional_service_id' => $ps->professional_service_id,
                'person_id' => $ps->professional->person_id,
                'first_name' => $ps->professional->person->first_name,
                'last_name' => $ps->professional->person->last_name,
                'email' => $ps->professional->person->userAccount->email ?? null,
                'specialty' => $ps->professional->specialty,
                'title' => $ps->professional->title,
                'identification' => $ps->professional->person->identification->number ?? null,
            ];
        }));
    }

    /**
     * Get all services with their professionals.
     * GET /api/professional-services/services-with-professionals
     */
    public function getServicesWithProfessionals()
    {
        $services = Service::with([
            'professionalServices.professional.person.userAccount',
            'professionalServices.professional.person.identification',
        ])->get();

        return response()->json($services->map(function ($service) {
            return [
                'service_id' => $service->service_id,
                'name' => $service->name,
                'price' => $service->price,
                'professionals' => $service->professionalServices->map(function ($ps) {
                    return [
                        'professional_service_id' => $ps->professional_service_id,
                        'person_id' => $ps->professional->person_id,
                        'first_name' => $ps->professional->person->first_name,
                        'last_name' => $ps->professional->person->last_name,
                        'email' => $ps->professional->person->userAccount->email ?? null,
                        'specialty' => $ps->professional->specialty,
                        'title' => $ps->professional->title,
                        'identification' => $ps->professional->person->identification->number ?? null,
                    ];
                }),
                'total_professionals' => $service->professionalServices->count(),
            ];
        }));
    }
}
