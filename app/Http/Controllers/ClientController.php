<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Person;

class ClientController extends Controller
{
    /**
     * Display a listing of clients.
     * GET /api/clients
     */
    public function index()
    {
        $clients = Client::with([
            'person.userAccount.role',
            'person.userAccount.accountStatus',
            'person.genderInfo',
            'person.occupationInfo',
            'person.maritalStatusInfo',
            'person.educationInfo',
            'person.identification',
        ])->get();

        return response()->json($clients->map(function ($client) {
            $person = $client->person;
            
            return [
                'person_id' => $client->person_id,
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
                ],
                'user_account' => [
                    'user_account_id' => $person->userAccount->user_account_id,
                    'email' => $person->userAccount->email,
                    'role' => $person->userAccount->role->name,
                    'status' => $person->userAccount->accountStatus->name,
                    'last_login' => $person->userAccount->last_login,
                ],
                'created_by' => $client->created_by,
                'creation_date' => $client->creation_date,
                'modified_by' => $client->modified_by,
                'modification_date' => $client->modification_date,
            ];
        }));
    }

    /**
     * Store a newly created client.
     * POST /api/clients
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'person_id' => 'required|exists:person,person_id|unique:client,person_id',
            'created_by' => 'nullable|string|max:255',
        ]);

        // Verificar que la persona no sea ya profesional o staff
        $person = Person::with(['professional', 'staff'])->findOrFail($validated['person_id']);
        
        if ($person->professional) {
            return response()->json([
                'message' => 'Esta persona ya está registrada como profesional'
            ], 409);
        }
        
        if ($person->staff) {
            return response()->json([
                'message' => 'Esta persona ya está registrada como staff'
            ], 409);
        }

        $client = Client::create($validated);

        return response()->json([
            'message' => 'Client created successfully',
            'client' => $client->load('person.userAccount', 'person.identification')
        ], 201);
    }

    /**
     * Display the specified client.
     * GET /api/clients/{id}
     */
    public function show(string $id)
    {
        $client = Client::with([
            'person.userAccount.role',
            'person.userAccount.accountStatus',
            'person.genderInfo',
            'person.occupationInfo',
            'person.maritalStatusInfo',
            'person.educationInfo',
            'person.identification',
        ])->findOrFail($id);

        $person = $client->person;

        return response()->json([
            'person_id' => $client->person_id,
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
            ],
            'user_account' => [
                'user_account_id' => $person->userAccount->user_account_id,
                'email' => $person->userAccount->email,
                'role' => $person->userAccount->role->name,
                'status' => $person->userAccount->accountStatus->name,
                'last_login' => $person->userAccount->last_login,
            ],
            'created_by' => $client->created_by,
            'creation_date' => $client->creation_date,
            'modified_by' => $client->modified_by,
            'modification_date' => $client->modification_date,
        ]);
    }

    /**
     * Update the specified client.
     * PUT/PATCH /api/clients/{id}
     */
    public function update(Request $request, string $id)
    {
        $client = Client::findOrFail($id);

        $validated = $request->validate([
            'modified_by' => 'nullable|string|max:255',
        ]);

        $validated['modification_date'] = now();
        $client->update($validated);

        return response()->json([
            'message' => 'Client updated successfully',
            'client' => $client->load('person.userAccount')
        ]);
    }

    /**
     * Remove the specified client.
     * DELETE /api/clients/{id}
     */
    public function destroy(string $id)
    {
        $client = Client::findOrFail($id);

        // Verificar si tiene pagos o citas asociadas
        $hasPayments = $client->payments()->exists();
        
        if ($hasPayments) {
            return response()->json([
                'message' => 'No se puede eliminar el cliente porque tiene pagos o citas asociadas'
            ], 409);
        }

        $client->delete();

        return response()->json([
            'message' => 'Client deleted successfully'
        ]);
    }
}
