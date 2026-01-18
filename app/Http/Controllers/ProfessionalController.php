<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Professional;
use App\Models\Person;

class ProfessionalController extends Controller
{
    /**
     * Display a listing of professionals with full details.
     * GET /api/professionals
     */
    public function index()
    {
        $professionals = Professional::with([
            'person.userAccount.role',
            'person.userAccount.accountStatus',
            'person.genderInfo',
            'person.occupationInfo',
            'person.maritalStatusInfo',
            'person.educationInfo',
            'person.country',
            'person.identification',
            'professionalServices.service',
        ])->get();

        return response()->json($professionals->map(function ($professional) {
            $person = $professional->person;
            
            return [
                'person_id' => $professional->person_id,
                'specialty' => $professional->specialty,
                'title' => $professional->title,
                'person' => [
                    'first_name' => $person->first_name,
                    'last_name' => $person->last_name,
                    'identification' => $person->identification->number ?? null,
                ],
                'user_account' => [
                    'user_account_id' => $person->userAccount->user_account_id,
                    'email' => $person->userAccount->email,
                    'role' => $person->userAccount->role->name,
                    'status' => $person->userAccount->accountStatus->name,
                ],
                'services' => $professional->professionalServices->map(function ($ps) {
                    return [
                        'service_id' => $ps->service->service_id,
                        'name' => $ps->service->name,
                        'price' => $ps->service->price,
                    ];
                }),
                'total_services' => $professional->professionalServices->count(),
                'created_by' => $professional->created_by,
                'creation_date' => $professional->creation_date,
                'modified_by' => $professional->modified_by,
                'modification_date' => $professional->modification_date,
            ];
        }));
    }

    /**
     * Store a newly created professional.
     * POST /api/professionals
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'person_id' => 'required|exists:person,person_id|unique:professional,person_id',
            'specialty' => 'required|string|max:255',
            'title' => 'required|string|max:50',
            'created_by' => 'nullable|string|max:255',
        ]);

        // Verificar que la persona no sea ya cliente o staff
        $person = Person::with(['client', 'staff'])->findOrFail($validated['person_id']);
        
        if ($person->client) {
            return response()->json([
                'message' => 'Esta persona ya está registrada como cliente'
            ], 409);
        }
        
        if ($person->staff) {
            return response()->json([
                'message' => 'Esta persona ya está registrada como staff'
            ], 409);
        }

        $professional = Professional::create($validated);

        return response()->json([
            'message' => 'Professional created successfully',
            'professional' => $professional->load('person.userAccount', 'person.identification')
        ], 201);
    }

    /**
     * Display the specified professional.
     * GET /api/professionals/{id}
     */
    public function show(string $id)
    {
        $professional = Professional::with([
            'person.userAccount.role',
            'person.userAccount.accountStatus',
            'person.genderInfo',
            'person.occupationInfo',
            'person.maritalStatusInfo',
            'person.educationInfo',
            'person.country',
            'person.identification',
            'professionalServices.service',
        ])->findOrFail($id);

        $person = $professional->person;

        return response()->json([
            'person_id' => $professional->person_id,
            'specialty' => $professional->specialty,
            'title' => $professional->title,
            'person' => [
                'first_name' => $person->first_name,
                'last_name' => $person->last_name,
                'birthdate' => $person->birthdate,
                'phone' => $person->phone,
                'identification' => $person->identification->number ?? null,
                'gender' => $person->genderInfo->name ?? null,
                'occupation' => $person->occupationInfo->name ?? null,
                'marital_status' => $person->maritalStatusInfo->name ?? null,
                'education' => $person->educationInfo->name ?? null,
                'country' => $person->country->name ?? null,
            ],
            'user_account' => [
                'user_account_id' => $person->userAccount->user_account_id,
                'email' => $person->userAccount->email,
                'role' => $person->userAccount->role->name,
                'status' => $person->userAccount->accountStatus->name,
                'last_login' => $person->userAccount->last_login,
            ],
            'services' => $professional->professionalServices->map(function ($ps) {
                return [
                    'professional_service_id' => $ps->professional_service_id,
                    'service_id' => $ps->service->service_id,
                    'name' => $ps->service->name,
                    'price' => $ps->service->price,
                ];
            }),
            'total_services' => $professional->professionalServices->count(),
            'created_by' => $professional->created_by,
            'creation_date' => $professional->creation_date,
            'modified_by' => $professional->modified_by,
            'modification_date' => $professional->modification_date,
        ]);
    }

    /**
     * Update the specified professional.
     * PUT/PATCH /api/professionals/{id}
     */
    public function update(Request $request, string $id)
    {
        $professional = Professional::findOrFail($id);

        $validated = $request->validate([
            'specialty' => 'sometimes|string|max:255',
            'title' => 'sometimes|string|max:50',
            'modified_by' => 'nullable|string|max:255',
        ]);

        $validated['modification_date'] = now();
        $professional->update($validated);

        return response()->json([
            'message' => 'Professional updated successfully',
            'professional' => $professional->load('person.userAccount', 'professionalServices.service')
        ]);
    }

    /**
     * Remove the specified professional.
     * DELETE /api/professionals/{id}
     */
    public function destroy(string $id)
    {
        $professional = Professional::findOrFail($id);

        // Verificar si tiene servicios o citas asociadas
        $hasServices = $professional->professionalServices()->exists();
        
        if ($hasServices) {
            return response()->json([
                'message' => 'No se puede eliminar el profesional porque tiene servicios asignados. Elimina primero las asignaciones de servicios.'
            ], 409);
        }

        $professional->delete();

        return response()->json([
            'message' => 'Professional deleted successfully'
        ]);
    }

    /**
     * Get professionals by specialty.
     * GET /api/professionals/specialty/{specialty}
     */
    public function getBySpecialty(string $specialty)
    {
        $professionals = Professional::with([
            'person.userAccount',
            'person.identification',
            'professionalServices.service',
        ])->where('specialty', 'LIKE', '%' . $specialty . '%')
            ->get();

        return response()->json($professionals->map(function ($professional) {
            return [
                'person_id' => $professional->person_id,
                'first_name' => $professional->person->first_name,
                'last_name' => $professional->person->last_name,
                'email' => $professional->person->userAccount->email ?? null,
                'phone' => $professional->person->phone,
                'specialty' => $professional->specialty,
                'title' => $professional->title,
                'identification' => $professional->person->identification->number ?? null,
                'services' => $professional->professionalServices->map(function ($ps) {
                    return [
                        'service_id' => $ps->service->service_id,
                        'name' => $ps->service->name,
                        'price' => $ps->service->price,
                    ];
                }),
            ];
        }));
    }

    /**
     * Get professionals available for a service.
     * GET /api/professionals/service/{serviceId}
     */
    public function getByService(string $serviceId)
    {
        $professionals = Professional::with([
            'person.userAccount',
            'person.identification',
        ])->whereHas('professionalServices', function ($query) use ($serviceId) {
            $query->where('service_id', $serviceId);
        })->get();

        return response()->json($professionals->map(function ($professional) {
            return [
                'person_id' => $professional->person_id,
                'first_name' => $professional->person->first_name,
                'last_name' => $professional->person->last_name,
                'email' => $professional->person->userAccount->email ?? null,
                'phone' => $professional->person->phone,
                'specialty' => $professional->specialty,
                'title' => $professional->title,
                'identification' => $professional->person->identification->number ?? null,
            ];
        }));
    }

    /**
     * Get professionals with their statistics.
     * GET /api/professionals/statistics
     */
    public function getStatistics()
    {
        $professionals = Professional::with([
            'person.userAccount',
            'professionalServices.service',
            'person.workerSchedules.appointment',
        ])->get();

        return response()->json($professionals->map(function ($professional) {
            $completedAppointments = $professional->person->workerSchedules
                ->flatMap(function ($ws) {
                    return $ws->appointment ? [$ws->appointment] : [];
                })
                ->where('status', 2) // Status 2 = Completed
                ->count();

            return [
                'person_id' => $professional->person_id,
                'first_name' => $professional->person->first_name,
                'last_name' => $professional->person->last_name,
                'email' => $professional->person->userAccount->email ?? null,
                'specialty' => $professional->specialty,
                'title' => $professional->title,
                'total_services' => $professional->professionalServices->count(),
                'total_appointments_completed' => $completedAppointments,
            ];
        }));
    }
}
