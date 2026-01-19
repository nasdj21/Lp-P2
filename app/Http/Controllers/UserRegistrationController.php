<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserAccount;
use App\Models\Person;
use App\Models\Identification;
use App\Models\Client;
use App\Models\Professional;
use App\Models\Staff;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UserRegistrationController extends Controller
{
    /**
     * Register a complete user (UserAccount + Person + Identification + Role-specific table)
     * POST /api/register-complete-user
     */
    public function registerCompleteUser(Request $request)
    {
        // Validar datos
        $validated = $request->validate([
            // UserAccount
            'email' => 'required|email|unique:user_account,email',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:role,role_id|in:2,3,4', // 2=Professional, 3=Client, 4=Staff
            'status' => 'nullable|exists:user_account_status,status_id',
            
            // Person
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'birthdate' => 'required|date|before_or_equal:today',
            'gender' => 'required|exists:gender,gender_id',
            'occupation' => 'required|exists:occupation,occupation_id',
            'marital_status' => 'required|exists:marital_status,marital_status_id',
            'education' => 'required|exists:education,education_id',
            'phone' => 'required|string|regex:/^\d{8,10}$/',
            'country_id' => 'nullable|exists:country,country_id',
            
            // Identification
            'identification_number' => 'required|string|max:13|unique:identification,number',
            
            // Professional (solo si role_id = 2)
            'specialty' => 'required_if:role_id,2|nullable|string|max:255',
            'title' => 'required_if:role_id,2|nullable|string|max:50',
            
            // Metadata
            'created_by' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            // 1. Crear UserAccount
            $userAccount = UserAccount::create([
                'role_id' => $validated['role_id'],
                'email' => $validated['email'],
                'password_hash' => hash('sha256', $validated['password']),
                'status' => $validated['status'] ?? 1, // Por defecto: Active
                'created_by' => $validated['created_by'] ?? 'system',
            ]);

            // 2. Crear Person
            $person = Person::create([
                'user_account_id' => $userAccount->user_account_id,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'birthdate' => $validated['birthdate'],
                'gender' => $validated['gender'],
                'occupation' => $validated['occupation'],
                'marital_status' => $validated['marital_status'],
                'education' => $validated['education'],
                'phone' => $validated['phone'],
                'country_id' => $validated['country_id'] ?? null,
                'created_by' => $validated['created_by'] ?? 'system',
            ]);

            // 3. Crear Identification
            $identification = Identification::create([
                'person_id' => $person->person_id,
                'number' => $validated['identification_number'],
                'created_by' => $validated['created_by'] ?? 'system',
            ]);

            // 4. Crear registro en tabla específica según role_id
            $roleSpecificRecord = null;

            switch ($validated['role_id']) {
                case 2: // Professional
                    $roleSpecificRecord = Professional::create([
                        'person_id' => $person->person_id,
                        'specialty' => $validated['specialty'],
                        'title' => $validated['title'],
                        'created_by' => $validated['created_by'] ?? 'system',
                    ]);
                    break;

                case 3: // Client
                    $roleSpecificRecord = Client::create([
                        'person_id' => $person->person_id,
                        'created_by' => $validated['created_by'] ?? 'system',
                    ]);
                    break;

                case 4: // Staff
                    $roleSpecificRecord = Staff::create([
                        'person_id' => $person->person_id,
                        'created_by' => $validated['created_by'] ?? 'system',
                    ]);
                    break;
            }

            DB::commit();

            // Cargar relaciones para respuesta completa
            $userAccount->load([
                'role',
                'accountStatus',
                'person.genderInfo',
                'person.occupationInfo',
                'person.maritalStatusInfo',
                'person.educationInfo',
                'person.country',
                'person.identification',
                'person.client',
                'person.professional',
                'person.staff',
            ]);

            return response()->json([
                'message' => 'Usuario registrado exitosamente',
                'user' => [
                    'user_account_id' => $userAccount->user_account_id,
                    'email' => $userAccount->email,
                    'role' => [
                        'role_id' => $userAccount->role->role_id,
                        'name' => $userAccount->role->name,
                    ],
                    'status' => [
                        'status_id' => $userAccount->accountStatus->status_id,
                        'name' => $userAccount->accountStatus->name,
                    ],
                    'person' => [
                        'person_id' => $person->person_id,
                        'first_name' => $person->first_name,
                        'last_name' => $person->last_name,
                        'birthdate' => $person->birthdate,
                        'phone' => $person->phone,
                        'identification' => $identification->number,
                        'gender' => $person->genderInfo->name,
                        'occupation' => $person->occupationInfo->name,
                        'marital_status' => $person->maritalStatusInfo->name,
                        'education' => $person->educationInfo->name,
                        'country' => $person->country->name ?? null,
                    ],
                    'role_type' => $validated['role_id'] == 2 ? 'professional' : 
                                  ($validated['role_id'] == 3 ? 'client' : 'staff'),
                    'professional_data' => $validated['role_id'] == 2 ? [
                        'specialty' => $validated['specialty'],
                        'title' => $validated['title'],
                    ] : null,
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Error al registrar el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a complete user (UserAccount + Person + Identification + Role-specific)
     * PUT /api/update-complete-user/{user_account_id}
     */
public function updateCompleteUser(Request $request, string $userAccountId)
{
    $userAccount = UserAccount::with('person.identification', 'person.professional', 'person.client', 'person.staff')
        ->findOrFail($userAccountId);

    // Validar datos (mismos campos que registro pero opcionales)
    $validated = $request->validate([
        // UserAccount
        'email' => 'sometimes|email|unique:user_account,email,' . $userAccountId . ',user_account_id',
        'password' => 'sometimes|string|min:6',
        'role_id' => 'sometimes|exists:role,role_id|in:2,3,4', // 2=Professional, 3=Client, 4=Staff
        'status' => 'sometimes|exists:user_account_status,status_id',
        
        // Person
        'first_name' => 'sometimes|string|max:255',
        'last_name' => 'sometimes|string|max:255',
        'birthdate' => 'sometimes|date|before_or_equal:today',
        'gender' => 'sometimes|exists:gender,gender_id',
        'occupation' => 'sometimes|exists:occupation,occupation_id',
        'marital_status' => 'sometimes|exists:marital_status,marital_status_id',
        'education' => 'sometimes|exists:education,education_id',
        'phone' => 'sometimes|string|regex:/^\d{8,10}$/',
        'country_id' => 'sometimes|nullable|exists:country,country_id',
        
        // Identification
        'identification_number' => 'sometimes|string|max:13|unique:identification,number,' . 
            ($userAccount->person->identification->identification_id ?? 'NULL') . ',identification_id',
        
        // Professional (solo si role_id = 2)
        'specialty' => 'sometimes|nullable|string|max:255',
        'title' => 'sometimes|nullable|string|max:50',
        
        // Metadata
        'modified_by' => 'nullable|string|max:255',
    ]);

    DB::beginTransaction();

    try {
        // 1. Actualizar UserAccount
        $userAccountData = [];
        if (isset($validated['email'])) {
            $userAccountData['email'] = $validated['email'];
        }
        if (isset($validated['password'])) {
            $userAccountData['password_hash'] = hash('sha256', $validated['password']);
        }
        if (isset($validated['status'])) {
            $userAccountData['status'] = $validated['status'];
        }
        
        if (!empty($userAccountData)) {
            $userAccountData['modified_by'] = $validated['modified_by'] ?? 'system';
            $userAccountData['modification_date'] = now();
            $userAccount->update($userAccountData);
        }

        // 2. Cambio de rol si se especifica role_id
        if (isset($validated['role_id']) && $validated['role_id'] != $userAccount->role_id) {
            // Eliminar registro de rol anterior
            if ($userAccount->person->professional) {
                $userAccount->person->professional->delete();
            }
            if ($userAccount->person->client) {
                $userAccount->person->client->delete();
            }
            if ($userAccount->person->staff) {
                $userAccount->person->staff->delete();
            }

            // Actualizar role_id en user_account
            $userAccount->update([
                'role_id' => $validated['role_id'],
                'modified_by' => $validated['modified_by'] ?? 'system',
                'modification_date' => now(),
            ]);

            // Crear nuevo registro según el nuevo rol
            switch ($validated['role_id']) {
                case 2: // Professional
                    Professional::create([
                        'person_id' => $userAccount->person->person_id,
                        'specialty' => $validated['specialty'] ?? 'No especificado',
                        'title' => $validated['title'] ?? 'N/A',
                        'created_by' => $validated['modified_by'] ?? 'system',
                    ]);
                    break;

                case 3: // Client
                    Client::create([
                        'person_id' => $userAccount->person->person_id,
                        'created_by' => $validated['modified_by'] ?? 'system',
                    ]);
                    break;

                case 4: // Staff
                    Staff::create([
                        'person_id' => $userAccount->person->person_id,
                        'created_by' => $validated['modified_by'] ?? 'system',
                    ]);
                    break;
            }
        }

        // 3. Actualizar Person
        if ($userAccount->person) {
            $personData = [];
            $personFields = [
                'first_name', 'last_name', 'birthdate', 'gender', 'occupation', 
                'marital_status', 'education', 'phone', 'country_id'
            ];
            
            foreach ($personFields as $field) {
                if (isset($validated[$field])) {
                    $personData[$field] = $validated[$field];
                }
            }
            
            if (!empty($personData)) {
                $personData['modified_by'] = $validated['modified_by'] ?? 'system';
                $personData['modification_date'] = now();
                $userAccount->person->update($personData);
            }

            // 4. Actualizar Identification
            if (isset($validated['identification_number']) && $userAccount->person->identification) {
                $userAccount->person->identification->update([
                    'number' => $validated['identification_number'],
                    'modified_by' => $validated['modified_by'] ?? 'system',
                    'modification_date' => now(),
                ]);
            }

            // 5. Actualizar Professional (si es profesional y no hubo cambio de rol)
            if (!isset($validated['role_id']) && $userAccount->person->professional) {
                $professionalData = [];
                
                if (isset($validated['specialty'])) {
                    $professionalData['specialty'] = $validated['specialty'];
                }
                if (isset($validated['title'])) {
                    $professionalData['title'] = $validated['title'];
                }
                
                if (!empty($professionalData)) {
                    $professionalData['modified_by'] = $validated['modified_by'] ?? 'system';
                    $professionalData['modification_date'] = now();
                    $userAccount->person->professional->update($professionalData);
                }
            }
        }

        DB::commit();

        // Recargar todas las relaciones
        $userAccount->load([
            'role',
            'accountStatus',
            'person.genderInfo',
            'person.occupationInfo',
            'person.maritalStatusInfo',
            'person.educationInfo',
            'person.country',
            'person.identification',
            'person.client',
            'person.professional',
            'person.staff',
        ]);

        $person = $userAccount->person;

        return response()->json([
            'message' => 'Usuario actualizado exitosamente',
            'user' => [
                'user_account_id' => $userAccount->user_account_id,
                'email' => $userAccount->email,
                'role' => [
                    'role_id' => $userAccount->role->role_id,
                    'name' => $userAccount->role->name,
                ],
                'status' => [
                    'status_id' => $userAccount->accountStatus->status_id,
                    'name' => $userAccount->accountStatus->name,
                ],
                'person' => [
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
                    'country' => $person->country->name ?? null,
                ],
                'role_type' => $person->professional ? 'professional' : 
                              ($person->client ? 'client' : 
                              ($person->staff ? 'staff' : null)),
                'professional_data' => $person->professional ? [
                    'specialty' => $person->professional->specialty,
                    'title' => $person->professional->title,
                ] : null,
            ],
        ]);

    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'message' => 'Error al actualizar el usuario',
            'error' => $e->getMessage()
        ], 500);
    }
    }
}
