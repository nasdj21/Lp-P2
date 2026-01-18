<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Person;

class PersonController extends Controller
{
    /**
     * Display a listing of persons with full details.
     * GET /api/persons
     */
    public function index()
    {
        $persons = Person::with([
            'userAccount.role',
            'userAccount.accountStatus',
            'genderInfo',
            'occupationInfo',
            'maritalStatusInfo',
            'educationInfo',
            'country',
            'identification',
            'client',
            'professional.services',
            'staff'
        ])->get();

        return response()->json($persons->map(function ($person) {
            return [
                'person_id' => $person->person_id,
                'first_name' => $person->first_name,
                'last_name' => $person->last_name,
                'birthdate' => $person->birthdate,
                'phone' => $person->phone,
                'email' => $person->userAccount->email,
                'user_account' => [
                    'user_account_id' => $person->userAccount->user_account_id,                    
                    'role' => $person->userAccount->role->name,
                    'status_id' => $person->userAccount->accountStatus->account_status_id,
                    'status' => $person->userAccount->accountStatus->name,
                ],
                'identification' => $person->identification->number ?? null,
                'gender' => $person->genderInfo->name ?? null,
                'occupation' => $person->occupationInfo->name ?? null,
                'marital_status' => $person->maritalStatusInfo->name ?? null,
                'education' => $person->educationInfo->name ?? null,
                'country' => [
                    'country_id' => $person->country->country_id ?? null,
                    'name' => $person->country->name ?? null,
                    'state' => [
                        'state_id' => $person->country->state->state_id ?? null,
                        'name' => $person->country->state->name ?? null,
                        'city' => [
                            'city_id' => $person->country->state->city->city_id ?? null,
                            'name' => $person->country->state->city->name ?? null,
                        ]
                    ]
                ],
                'type' => $person->client ? 'Client' : ($person->professional ? 'Professional' : ($person->staff ? 'Staff' : null)),
                'professional_info' => $person->professional ? [
                    'specialty' => $person->professional->specialty,
                    'title' => $person->professional->title,
                    'services' => $person->professional->services->map(function ($service) {
                        return [
                            'service_id' => $service->service_id,
                            'name' => $service->name,
                            'price' => $service->price,
                        ];
                    }),
                ] : null,
                'created_by' => $person->created_by,
                'creation_date' => $person->creation_date,
            ];
        }));
    }

    /**
     * Store a newly created person.
     * POST /api/persons
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_account_id' => 'required|exists:user_account,user_account_id|unique:person,user_account_id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'birthdate' => 'required|date|before_or_equal:today',
            'gender' => 'required|exists:gender,gender_id',
            'occupation' => 'required|exists:occupation,occupation_id',
            'marital_status' => 'required|exists:marital_status,marital_status_id',
            'education' => 'required|exists:education,education_id',
            'phone' => 'required|string|regex:/^\d{8,10}$/',
            'country_id' => 'nullable|exists:country,country_id',
            'created_by' => 'nullable|string|max:255',
        ]);

        $person = Person::create($validated);

        return response()->json([
            'message' => 'Person created successfully',
            'person' => $person->load([
                'userAccount.role',
                'genderInfo',
                'occupationInfo',
                'maritalStatusInfo',
                'educationInfo',
                'country'
            ])
        ], 201);
    }

    /**
     * Display the specified person.
     * GET /api/persons/{id}
     */
    public function show(string $id)
    {
        $person = Person::with([
            'userAccount.role',
            'userAccount.accountStatus',
            'genderInfo',
            'occupationInfo',
            'maritalStatusInfo',
            'educationInfo',
            'country',
            'identification',
            'client',
            'professional.services',
            'staff'
        ])->findOrFail($id);

        return response()->json([
            'person_id' => $person->person_id,
            'first_name' => $person->first_name,
            'last_name' => $person->last_name,
            'birthdate' => $person->birthdate,
            'phone' => $person->phone,
            'user_account' => [
                'user_account_id' => $person->userAccount->user_account_id,
                'email' => $person->userAccount->email,
                'role' => $person->userAccount->role->name,
                'status' => $person->userAccount->accountStatus->name,
            ],
            'identification' => $person->identification->number ?? null,
            'gender' => $person->genderInfo->name ?? null,
            'occupation' => $person->occupationInfo->name ?? null,
            'marital_status' => $person->maritalStatusInfo->name ?? null,
            'education' => $person->educationInfo->name ?? null,
            'country' => $person->country->name ?? null,
            'type' => $person->client ? 'Client' : ($person->professional ? 'Professional' : ($person->staff ? 'Staff' : null)),
            'professional_info' => $person->professional ? [
                'specialty' => $person->professional->specialty,
                'title' => $person->professional->title,
                'services' => $person->professional->services->map(function ($service) {
                    return [
                        'service_id' => $service->service_id,
                        'name' => $service->name,
                        'price' => $service->price,
                    ];
                }),
            ] : null,
            'created_by' => $person->created_by,
            'creation_date' => $person->creation_date,
        ]);
    }

    /**
     * Update the specified person.
     * PUT/PATCH /api/persons/{id}
     */
    public function update(Request $request, string $id)
    {
        $person = Person::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'birthdate' => 'sometimes|date|before_or_equal:today',
            'gender' => 'sometimes|exists:gender,gender_id',
            'occupation' => 'sometimes|exists:occupation,occupation_id',
            'marital_status' => 'sometimes|exists:marital_status,marital_status_id',
            'education' => 'sometimes|exists:education,education_id',
            'phone' => 'sometimes|string|regex:/^\d{8,10}$/',
            'country_id' => 'sometimes|nullable|exists:country,country_id',
            'modified_by' => 'nullable|string|max:255',
        ]);

        $validated['modification_date'] = now();
        $person->update($validated);

        return response()->json([
            'message' => 'Person updated successfully',
            'person' => $person->load([
                'userAccount.role',
                'genderInfo',
                'occupationInfo',
                'maritalStatusInfo',
                'educationInfo',
                'country'
            ])
        ]);
    }

    /**
     * Remove the specified person.
     * DELETE /api/persons/{id}
     */
    public function destroy(string $id)
    {
        $person = Person::findOrFail($id);
        $person->delete();

        return response()->json([
            'message' => 'Person deleted successfully'
        ]);
    }
}
