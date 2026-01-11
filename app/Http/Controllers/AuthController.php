<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserAccount;

class AuthController extends Controller
{
    /**
     * Login
     * POST /api/login
     */
    public function login(Request $request)
    {   
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            // Buscar usuario por email
            $user = UserAccount::where('email', $request->email)->first();

            // Verificar si existe y si la contraseña es correcta
            if (!$user || hash('sha256', $request->password) !== $user->password_hash) {
                throw ValidationException::withMessages([
                    'email' => ['Las credenciales son incorrectas.'],
                ]);
            }

            // Verificar que el usuario esté activo
            if ($user->status != 1) {
                return response()->json([
                    'message' => 'Tu cuenta está inactiva. Contacta al administrador.'
                ], 403);
            }

            // Actualizar último login
            $user->update(['last_login' => now()]);

            // Crear token
            $token = $user->createToken('auth-token')->plainTextToken;

            // Cargar relaciones
            $user->load('role', 'accountStatus', 'person');

            return response()->json([
                'message' => 'Login exitoso',
                'user' => [
                    'user_account_id' => $user->user_account_id,
                    'email' => $user->email,
                    'role' => $user->role->name,
                    'status' => $user->accountStatus->name,
                    'person' => $user->person ? [
                        'first_name' => $user->person->first_name,
                        'last_name' => $user->person->last_name,
                    ] : null,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ], 200);
            
        } catch (\Exception $e) {
            // Log del error
            \Log::error('Error en login: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Error en el servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout
     * POST /api/logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada exitosamente'
        ], 200);
    }

    /**
     * Me
     * GET /api/me
     */
    public function me(Request $request)
    {
        $user = $request->user();
        $user->load('role', 'accountStatus', 'person.identification', 'person.genderInfo');

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
            'person' => $user->person ? [
                'person_id' => $user->person->person_id,
                'first_name' => $user->person->first_name,
                'last_name' => $user->person->last_name,
                'phone' => $user->person->phone,
                'identification' => $user->person->identification->number ?? null,
                'gender' => $user->person->genderInfo->name ?? null,
            ] : null,
        ]);
    }
}
