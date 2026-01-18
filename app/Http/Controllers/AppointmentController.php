<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Payment;
use App\Models\Receipt;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    /**
     * Display a listing of appointments with full details.
     * GET /api/appointments
     */
    public function index()
    {
        $appointments = Appointment::with([
            'payment.client.person.userAccount.role',
            'payment.client.person.genderInfo',
            'payment.client.person.identification',
            'payment.service',
            'payment.paymentStatus',
            'payment.receipt',
            'workerSchedule.schedule',
            'workerSchedule.person.professional',
            'workerSchedule.person.userAccount',
            'appointmentStatus',
            'scheduledByPerson.userAccount.role',
            'report'
        ])->get();

        return response()->json($appointments->map(function ($appointment) {
            return [
                'appointment_id' => $appointment->appointment_id,
                'client' => [
                    'person_id' => $appointment->payment->client->person->person_id,
                    'user_account_id' => $appointment->payment->client->person->userAccount->user_account_id,
                    'first_name' => $appointment->payment->client->person->first_name,
                    'last_name' => $appointment->payment->client->person->last_name,
                    'email' => $appointment->payment->client->person->userAccount->email,
                    'phone' => $appointment->payment->client->person->phone,
                    'identification' => $appointment->payment->client->person->identification->number ?? null,
                    'gender' => $appointment->payment->client->person->genderInfo->name ?? null,
                ],
                'professional' => [
                    'person_id' => $appointment->workerSchedule->person->person_id,
                    'first_name' => $appointment->workerSchedule->person->first_name,
                    'last_name' => $appointment->workerSchedule->person->last_name,
                    'specialty' => $appointment->workerSchedule->person->professional->specialty ?? null,
                    'title' => $appointment->workerSchedule->person->professional->title ?? null,
                    'email' => $appointment->workerSchedule->person->userAccount->email ?? null,
                ],
                'service' => [
                    'service_id' => $appointment->payment->service->service_id,
                    'name' => $appointment->payment->service->name,
                    'price' => $appointment->payment->service->price,
                ],
                'schedule' => [
                    'date' => $appointment->workerSchedule->schedule->date,
                    'start_time' => $appointment->workerSchedule->schedule->start_time,
                    'end_time' => $appointment->workerSchedule->schedule->end_time,
                ],
                'payment' => [
                    'payment_id' => $appointment->payment->payment_id,
                    'status' => $appointment->payment->paymentStatus->name,
                    'file' => $appointment->payment->file,
                    'has_receipt' => $appointment->payment->receipt ? true : false,
                ],
                'status' => [
                    'status_id' => $appointment->appointmentStatus->status_id,
                    'name' => $appointment->appointmentStatus->name,
                ],
                'scheduled_by' => [
                    'person_id' => $appointment->scheduledByPerson->person_id,
                    'first_name' => $appointment->scheduledByPerson->first_name,
                    'last_name' => $appointment->scheduledByPerson->last_name,
                    'role' => $appointment->scheduledByPerson->userAccount->role->name ?? null,
                ],
                'report' => $appointment->report ? [
                    'appointment_report_id' => $appointment->report->appointment_report_id,
                    'file' => $appointment->report->file,
                ] : null,
                'created_by' => $appointment->created_by,
                'creation_date' => $appointment->creation_date,
                'modified_by' => $appointment->modified_by,
                'modification_date' => $appointment->modification_date,
            ];
        }));
    }

    /**
     * Store a newly created appointment with payment and receipt.
     * POST /api/appointments
     * 
     * Request body:
     * {
     *   "service_id": 1,
     *   "client_id": 123,  // person_id del cliente
     *   "scheduled_by": 456, // person_id de quien agenda
     *   "worker_schedule_id": 789,
     *   "payment_status_id": 1, // 1=Pendiente, 2=Pagado, etc.
     *   "appointment_status_id": 1, // 1=Programada, etc.
     *   "payment_file": "ruta/comprobante.pdf", // opcional
     *   "created_by": "admin" // opcional
     * }
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:service,service_id',
            'client_id' => 'required|exists:person,person_id',
            'scheduled_by' => 'required|exists:person,person_id',
            'worker_schedule_id' => 'required|exists:worker_schedule,worker_schedule_id',
            'payment_status_id' => 'required|exists:payment_status,status_id',
            'appointment_status_id' => 'required|exists:appointment_status,status_id',
            'payment_file' => 'nullable|string',
            'created_by' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        
        try {
            // 1. Crear el pago
            $payment = Payment::create([
                'person_id' => $validated['client_id'],
                'service_id' => $validated['service_id'],
                'status_id' => $validated['payment_status_id'],
                'file' => $validated['payment_file'] ?? null,
                'created_by' => $validated['created_by'] ?? 'system',
            ]);

            // 2. Crear el recibo automáticamente
            $receipt = Receipt::create([
                'payment_id' => $payment->payment_id,
                'created_by' => $validated['created_by'] ?? 'system',
            ]);

            // 3. Crear la cita
            $appointment = Appointment::create([
                'payment_id' => $payment->payment_id,
                'scheduled_by' => $validated['scheduled_by'],
                'worker_schedule_id' => $validated['worker_schedule_id'],
                'status' => $validated['appointment_status_id'],
                'created_by' => $validated['created_by'] ?? 'system',
            ]);

            // 4. Marcar el horario como no disponible
            DB::table('worker_schedule')
                ->where('worker_schedule_id', $validated['worker_schedule_id'])
                ->update(['is_available' => false]);

            DB::commit();

            return response()->json([
                'message' => 'Appointment, payment and receipt created successfully',
                'appointment' => $appointment->load([
                    'payment.client.person',
                    'payment.service',
                    'payment.receipt',
                    'payment.paymentStatus',
                    'workerSchedule.schedule',
                    'workerSchedule.person.professional',
                    'appointmentStatus',
                    'scheduledByPerson'
                ]),
                'payment_id' => $payment->payment_id,
                'receipt_id' => $receipt->receipt_id,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error creating appointment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified appointment.
     * GET /api/appointments/{id}
     */
    public function show(string $id)
    {
        $appointment = Appointment::with([
            'payment.client.person.userAccount.role',
            'payment.client.person.genderInfo',
            'payment.client.person.identification',
            'payment.service',
            'payment.paymentStatus',
            'payment.receipt',
            'workerSchedule.schedule',
            'workerSchedule.person.professional',
            'workerSchedule.person.userAccount',
            'appointmentStatus',
            'scheduledByPerson.userAccount.role',
            'report'
        ])->findOrFail($id);

        return response()->json([
            'appointment_id' => $appointment->appointment_id,
            'client' => [
                'person_id' => $appointment->payment->client->person->person_id,
                'user_account_id' => $appointment->payment->client->person->userAccount->user_account_id,
                'first_name' => $appointment->payment->client->person->first_name,
                'last_name' => $appointment->payment->client->person->last_name,
                'email' => $appointment->payment->client->person->userAccount->email,
                'phone' => $appointment->payment->client->person->phone,
                'identification' => $appointment->payment->client->person->identification->number ?? null,
                'gender' => $appointment->payment->client->person->genderInfo->name ?? null,
            ],
            'professional' => [
                'person_id' => $appointment->workerSchedule->person->person_id,
                'first_name' => $appointment->workerSchedule->person->first_name,
                'last_name' => $appointment->workerSchedule->person->last_name,
                'specialty' => $appointment->workerSchedule->person->professional->specialty ?? null,
                'title' => $appointment->workerSchedule->person->professional->title ?? null,
                'email' => $appointment->workerSchedule->person->userAccount->email ?? null,
            ],
            'service' => [
                'service_id' => $appointment->payment->service->service_id,
                'name' => $appointment->payment->service->name,
                'price' => $appointment->payment->service->price,
            ],
            'schedule' => [
                'date' => $appointment->workerSchedule->schedule->date,
                'start_time' => $appointment->workerSchedule->schedule->start_time,
                'end_time' => $appointment->workerSchedule->schedule->end_time,
            ],
            'payment' => [
                'payment_id' => $appointment->payment->payment_id,
                'status' => $appointment->payment->paymentStatus->name,
                'file' => $appointment->payment->file,
                'has_receipt' => $appointment->payment->receipt ? true : false,
            ],
            'status' => [
                'status_id' => $appointment->appointmentStatus->status_id,
                'name' => $appointment->appointmentStatus->name,
            ],
            'scheduled_by' => [
                'person_id' => $appointment->scheduledByPerson->person_id,
                'first_name' => $appointment->scheduledByPerson->first_name,
                'last_name' => $appointment->scheduledByPerson->last_name,
                'role' => $appointment->scheduledByPerson->userAccount->role->name ?? null,
            ],
            'report' => $appointment->report ? [
                'appointment_report_id' => $appointment->report->appointment_report_id,
                'file' => $appointment->report->file,
            ] : null,
            'created_by' => $appointment->created_by,
            'creation_date' => $appointment->creation_date,
            'modified_by' => $appointment->modified_by,
            'modification_date' => $appointment->modification_date,
        ]);
    }

    /**
     * Update the specified appointment.
     * PUT/PATCH /api/appointments/{id}
     */
    public function update(Request $request, string $id)
    {
        $appointment = Appointment::findOrFail($id);

        $validated = $request->validate([
            'status' => 'sometimes|exists:appointment_status,status_id',
            'modified_by' => 'nullable|string|max:255',
        ]);

        $validated['modification_date'] = now();
        $appointment->update($validated);

        return response()->json([
            'message' => 'Appointment updated successfully',
            'appointment' => $appointment->load([
                'payment.client.person',
                'payment.service',
                'workerSchedule.schedule',
                'appointmentStatus'
            ])
        ]);
    }

    /**
     * Remove the specified appointment.
     * DELETE /api/appointments/{id}
     */
    public function destroy(string $id)
    {
        $appointment = Appointment::findOrFail($id);
        
        DB::beginTransaction();
        
        try {
            // Liberar el horario
            DB::table('worker_schedule')
                ->where('worker_schedule_id', $appointment->worker_schedule_id)
                ->update(['is_available' => true]);
            
            $appointment->delete();
            
            DB::commit();

            return response()->json([
                'message' => 'Appointment deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error deleting appointment',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
