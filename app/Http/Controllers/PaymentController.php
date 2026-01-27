<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments with full details.
     * GET /api/payments
     */
    public function index()
    {
        $payments = Payment::with([
            'client.person.userAccount',
            'client.person.identification',
            'service',
            'paymentStatus',
            'receipt',
            'appointment.appointmentStatus'
        ])->get();

        return response()->json($payments->map(function ($payment) {
            return [
                'payment_id' => $payment->payment_id,
                'client' => [
                    'person_id' => $payment->client->person->person_id,
                    'user_account_id' => $payment->client->person->userAccount->user_account_id,
                    'first_name' => $payment->client->person->first_name,
                    'last_name' => $payment->client->person->last_name,
                    'email' => $payment->client->person->userAccount->email,
                    'phone' => $payment->client->person->phone,
                    'identification' => $payment->client->person->identification->number ?? null,
                ],
                'service' => [
                    'service_id' => $payment->service->service_id,
                    'name' => $payment->service->name,
                    'price' => $payment->service->price,
                ],
                'status' => [
                    'status_id' => $payment->paymentStatus->status_id,
                    'name' => $payment->paymentStatus->name,
                ],
                'file' => $payment->file,
                'has_receipt' => $payment->receipt ? true : false,
                /*
                'receipt' => $payment->receipt ? [
                    'receipt_id' => $payment->receipt->receipt_id,
                    'creation_date' => $payment->receipt->creation_date,
                ] : null,*/
                'has_appointment' => $payment->appointment ? true : false,
                'appointment' => $payment->appointment ? [
                    'appointment_id' => $payment->appointment->appointment_id,
                    'status' => $payment->appointment->appointmentStatus->name,
                ] : null,
                'created_by' => $payment->created_by,
                'creation_date' => $payment->creation_date,
            ];
        }));
    }

    /**
     * Store a newly created payment.
     * POST /api/payments
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'person_id' => 'required|exists:client,person_id',
            'service_id' => 'required|exists:service,service_id',
            'status_id' => 'required|exists:payment_status,status_id',
            'file' => 'nullable|string|max:500',
            'created_by' => 'nullable|string|max:255',
        ]);

        $payment = Payment::create($validated);

        return response()->json([
            'message' => 'Payment created successfully',
            'payment' => $payment->load(['client.person', 'service', 'paymentStatus'])
        ], 201);
    }

    /**
     * Display the specified payment.
     * GET /api/payments/{id}
     */
    public function show(string $id)
    {
        $payment = Payment::with([
            'client.person.userAccount',
            'client.person.identification',
            'service',
            'paymentStatus',
            'receipt',
            'appointment.appointmentStatus'
        ])->findOrFail($id);

        return response()->json([
            'payment_id' => $payment->payment_id,
            'client' => [
                'person_id' => $payment->client->person->person_id,
                'user_account_id' => $payment->client->person->userAccount->user_account_id,
                'first_name' => $payment->client->person->first_name,
                'last_name' => $payment->client->person->last_name,
                'email' => $payment->client->person->userAccount->email,
                'phone' => $payment->client->person->phone,
                'identification' => $payment->client->person->identification->number ?? null,
            ],
            'service' => [
                'service_id' => $payment->service->service_id,
                'name' => $payment->service->name,
                'price' => $payment->service->price,
            ],
            'status' => [
                'status_id' => $payment->paymentStatus->status_id,
                'name' => $payment->paymentStatus->name,
            ],
            'file' => $payment->file,
            'has_receipt' => $payment->receipt ? true : false,
            'has_appointment' => $payment->appointment ? true : false,
            'appointment' => $payment->appointment ? [
                'appointment_id' => $payment->appointment->appointment_id,
                'status' => $payment->appointment->appointmentStatus->name,
            ] : null,
            'created_by' => $payment->created_by,
            'creation_date' => $payment->creation_date,
        ]);
    }

    /**
     * Update the specified payment.
     * PUT/PATCH /api/payments/{id}
     */
    public function update(Request $request, string $id)
    {
        $payment = Payment::findOrFail($id);

        $validated = $request->validate([
            'status_id' => 'sometimes|exists:payment_status,status_id',
            'file' => 'sometimes|nullable|string|max:500',
            'modified_by' => 'nullable|string|max:255',
        ]);

        $validated['modification_date'] = now();
        $payment->update($validated);

        return response()->json([
            'message' => 'Payment updated successfully',
            'payment' => $payment->load(['client.person', 'service', 'paymentStatus'])
        ]);
    }

    /**
     * Remove the specified payment.
     * DELETE /api/payments/{id}
     */
    public function destroy(string $id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();

        return response()->json([
            'message' => 'Payment deleted successfully'
        ]);
    }
}
