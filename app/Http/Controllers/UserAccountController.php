<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserAccount;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserAccountController extends Controller
{
    /**
     * Display a listing of user accounts with full details.
     * GET /api/users
     */
    public function index()
    {
        $users = UserAccount::with([
            'role',
            'accountStatus',
            'person.genderInfo',
            'person.occupationInfo',
            'person.maritalStatusInfo',
            'person.educationInfo',            
            'person.identification',
            'person.client',
            'person.professional.services',
            'person.staff'
        ])->get();

        return response()->json($users->map(function ($user) {
            $person = $user->person;
            
            return [
                'user_account_id' => $user->user_account_id,
                'email' => $user->email,
                'role' => [
                    'role_id' => $user->role->role_id,
                    'name' => $user->role->name,
                ],
                'status' => [
                    'status_id' => $user->accountStatus->status_id,
                    'name' => $user->accountStatus->name,
                ],
                'last_login' => $user->last_login,
                'person' => $person ? [
                    'person_id' => $person->person_id,
                    'first_name' => $person->first_name,
                    'last_name' => $person->last_name,
                    'birthdate' => $person->birthdate,
                    'phone' => $person->phone,
                    'identification' => $person->identification->number ?? null,
                    'gender' => $person->genderInfo->name ?? null,
                    'occupation' => $person->occupationInfo->name ?? null,
                    'marital_status' => $person->maritalStatusInfo->name ?? null,
                    'education' => $person->educationInfo->name ?? null,                    
                ] : null,
                'client' => $person && $person->client ? [
                    'person_id' => $person->client->person_id,
                ] : null,
                'professional' => $person && $person->professional ? [
                    'person_id' => $person->professional->person_id,
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
                'staff' => $person && $person->staff ? [
                    'person_id' => $person->staff->person_id,
                ] : null,
                'created_by' => $user->created_by,
                'creation_date' => $user->creation_date,
            ];
        }));
    }

    /**
     * Store a newly created user account.
     * POST /api/users
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:role,role_id',
            'email' => 'required|email|unique:user_account,email',
            'password' => 'required|string|min:6',
            'status' => 'required|exists:user_account_status,status_id',
            'created_by' => 'nullable|string|max:255',
        ]);

        $validated['password_hash'] = hash('sha256', $validated['password']);
        unset($validated['password']);

        $user = UserAccount::create($validated);

        return response()->json([
            'message' => 'User account created successfully',
            'user' => $user->load('role', 'accountStatus')
        ], 201);
    }

    /**
     * Display the specified user account.
     * GET /api/users/{id}
     */
    public function show(string $id)
    {
        $user = UserAccount::with([
            'role',
            'accountStatus',
            'person.genderInfo',
            'person.occupationInfo',
            'person.maritalStatusInfo',
            'person.educationInfo',            
            'person.identification',
            'person.client',
            'person.professional.services',
            'person.staff'
        ])->findOrFail($id);

        $person = $user->person;

        return response()->json([
            'user_account_id' => $user->user_account_id,
            'email' => $user->email,
            'role' => [
                'role_id' => $user->role->role_id,
                'name' => $user->role->name,
            ],
            'status' => [
                'status_id' => $user->accountStatus->status_id,
                'name' => $user->accountStatus->name,
            ],
            'last_login' => $user->last_login,
            'person' => $person ? [
                'person_id' => $person->person_id,
                'first_name' => $person->first_name,
                'last_name' => $person->last_name,
                'birthdate' => $person->birthdate,
                'phone' => $person->phone,
                'identification' => $person->identification->number ?? null,
                'gender' => $person->genderInfo->name ?? null,
                'occupation' => $person->occupationInfo->name ?? null,
                'marital_status' => $person->maritalStatusInfo->name ?? null,
                'education' => $person->educationInfo->name ?? null,                
            ] : null,
            'client' => $person && $person->client ? [
                'person_id' => $person->client->person_id,
            ] : null,
            'professional' => $person && $person->professional ? [
                'person_id' => $person->professional->person_id,
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
            'staff' => $person && $person->staff ? [
                'person_id' => $person->staff->person_id,
            ] : null,
            'created_by' => $user->created_by,
            'creation_date' => $user->creation_date,
        ]);
    }

    /**
     * Update the specified user account.
     * PUT/PATCH /api/users/{id}
     */
    public function update(Request $request, string $id)
    {
        $user = UserAccount::findOrFail($id);

        $validated = $request->validate([
            'email' => 'sometimes|email|unique:user_account,email,' . $id . ',user_account_id',
            'password' => 'sometimes|string|min:6',
            'status' => 'sometimes|exists:user_account_status,status_id',
            'modified_by' => 'nullable|string|max:255',
        ]);

        if (isset($validated['password'])) {
            $validated['password_hash'] = hash('sha256', $validated['password']);
            unset($validated['password']);
        }

        $validated['modification_date'] = now();
        $user->update($validated);

        return response()->json([
            'message' => 'User account updated successfully',
            'user' => $user->load('role', 'accountStatus')
        ]);
    }

    /**
     * Remove the specified user account.
     * DELETE /api/users/{id}
     */
    public function destroy(string $id)
    {
        $user = UserAccount::findOrFail($id);
        $user->delete();

        return response()->json([
            'message' => 'User account deleted successfully'
        ]);
    }
}
