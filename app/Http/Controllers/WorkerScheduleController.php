<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkerSchedule;
//use App\Models\Person;

class WorkerScheduleController extends Controller
{
    /**
     * Display a listing of worker schedules with full details.
     * GET /api/worker-schedules
     */
    public function index()
    {
        $workerSchedules = WorkerSchedule::with([
            'schedule',
            'person.userAccount',
            'person.professional',
            'person.identification',
        ])->get();

        return response()->json($workerSchedules->map(function ($ws) {
            return [
                'worker_schedule_id' => $ws->worker_schedule_id,
                'is_available' => $ws->is_available,
                'schedule' => [
                    'schedule_id' => $ws->schedule->schedule_id,
                    'date' => $ws->schedule->date,
                    'start_time' => $ws->schedule->start_time,
                    'end_time' => $ws->schedule->end_time,
                ],
                'worker' => [
                    'person_id' => $ws->person->person_id,
                    'first_name' => $ws->person->first_name,
                    'last_name' => $ws->person->last_name,
                    'email' => $ws->person->userAccount->email ?? null,
                    'phone' => $ws->person->phone,
                    'identification' => $ws->person->identification->number ?? null,                    
                    'specialty' => $ws->person->professional->specialty ?? null,
                    'title' => $ws->person->professional->title ?? null,
                    'gender' => $ws->person->genderInfo->name ?? null,
                ],
                'created_by' => $ws->created_by,
                'creation_date' => $ws->creation_date,
                'modified_by' => $ws->modified_by,
                'modification_date' => $ws->modification_date,
            ];
        }));
    }

    /**
     * Store a newly created worker schedule.
     * POST /api/worker-schedules
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'schedule_id' => 'required|exists:schedule,schedule_id',
            'person_id' => 'required|exists:person,person_id',
            'is_available' => 'required|boolean',
            'created_by' => 'nullable|string|max:255',
        ]);

        // Verificar que no exista ya esta combinación
        $exists = WorkerSchedule::where('schedule_id', $validated['schedule_id'])
            ->where('person_id', $validated['person_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Este trabajador ya tiene asignado este horario'
            ], 409);
        }

        $workerSchedule = WorkerSchedule::create($validated);

        return response()->json([
            'message' => 'Worker schedule created successfully',
            'worker_schedule' => $workerSchedule->load('schedule', 'person.userAccount')
        ], 201);
    }

    /**
     * Display the specified worker schedule.
     * GET /api/worker-schedules/{id}
     */
    public function show(string $id)
    {
        $ws = WorkerSchedule::with([
            'schedule',
            'person.userAccount',
            'person.professional',
            'person.identification',
        ])->findOrFail($id);

        return response()->json([
            'worker_schedule_id' => $ws->worker_schedule_id,
            'is_available' => $ws->is_available,
            'schedule' => [
                'schedule_id' => $ws->schedule->schedule_id,
                'date' => $ws->schedule->date,
                'start_time' => $ws->schedule->start_time,
                'end_time' => $ws->schedule->end_time,
            ],
            'worker' => [
                'person_id' => $ws->person->person_id,
                'first_name' => $ws->person->first_name,
                'last_name' => $ws->person->last_name,
                'email' => $ws->person->userAccount->email ?? null,
                'phone' => $ws->person->phone,
                'identification' => $ws->person->identification->number ?? null,
                'is_professional' => $ws->person->professional ? true : false,
                'specialty' => $ws->person->professional->specialty ?? null,
                'title' => $ws->person->professional->title ?? null,
            ],
            'created_by' => $ws->created_by,
            'creation_date' => $ws->creation_date,
            'modified_by' => $ws->modified_by,
            'modification_date' => $ws->modification_date,
        ]);
    }

    /**
     * Update the specified worker schedule.
     * PUT/PATCH /api/worker-schedules/{id}
     */
    public function update(Request $request, string $id)
    {
        $workerSchedule = WorkerSchedule::findOrFail($id);

        $validated = $request->validate([
            'is_available' => 'sometimes|boolean',
            'modified_by' => 'nullable|string|max:255',
        ]);

        $validated['modification_date'] = now();
        $workerSchedule->update($validated);

        return response()->json([
            'message' => 'Worker schedule updated successfully',
            'worker_schedule' => $workerSchedule->load('schedule', 'person.userAccount')
        ]);
    }

    /**
     * Remove the specified worker schedule.
     * DELETE /api/worker-schedules/{id}
     */
    public function destroy(string $id)
    {
        $workerSchedule = WorkerSchedule::findOrFail($id);
        $workerSchedule->delete();

        return response()->json([
            'message' => 'Worker schedule deleted successfully'
        ]);
    }

    /**
     * Get available worker schedules.
     * GET /api/worker-schedules/available
     */
    public function getAvailable()
    {
        $workerSchedules = WorkerSchedule::with([
            'schedule',
            'person.userAccount',
            'person.professional',
        ])->where('is_available', true)
            ->get();

        return response()->json($workerSchedules->map(function ($ws) {
            return [
                'worker_schedule_id' => $ws->worker_schedule_id,
                'is_available' => $ws->is_available,
                'schedule' => [
                    'schedule_id' => $ws->schedule->schedule_id,
                    'date' => $ws->schedule->date,
                    'start_time' => $ws->schedule->start_time,
                    'end_time' => $ws->schedule->end_time,
                ],
                'worker' => [
                    'person_id' => $ws->person->person_id,
                    'first_name' => $ws->person->first_name,
                    'last_name' => $ws->person->last_name,
                    'email' => $ws->person->userAccount->email ?? null,
                    'specialty' => $ws->person->professional->specialty ?? null,
                    'title' => $ws->person->professional->title ?? null,
                ],
            ];
        }));
    }

    /**
     * Get worker schedules by professional.
     * GET /api/worker-schedules/professional/{professionalId}
     */
    public function getByProfessional(string $professionalId)
    {
        $workerSchedules = WorkerSchedule::with('schedule')
            ->where('person_id', $professionalId)
            ->get();

        return response()->json($workerSchedules->map(function ($ws) {
            return [
                'worker_schedule_id' => $ws->worker_schedule_id,
                'is_available' => $ws->is_available,
                'schedule' => [
                    'schedule_id' => $ws->schedule->schedule_id,
                    'date' => $ws->schedule->date,
                    'start_time' => $ws->schedule->start_time,
                    'end_time' => $ws->schedule->end_time,
                ],
            ];
        }));
    }

    /**
     * Get worker schedules by date.
     * GET /api/worker-schedules/date/{date}
     */
    public function getByDate(string $date)
    {
        $workerSchedules = WorkerSchedule::with([
            'schedule',
            'person.userAccount',
            'person.professional',
        ])->whereHas('schedule', function ($query) use ($date) {
            $query->where('date', $date);
        })->get();

        return response()->json($workerSchedules->map(function ($ws) {
            return [
                'worker_schedule_id' => $ws->worker_schedule_id,
                'is_available' => $ws->is_available,
                'schedule' => [
                    'schedule_id' => $ws->schedule->schedule_id,
                    'date' => $ws->schedule->date,
                    'start_time' => $ws->schedule->start_time,
                    'end_time' => $ws->schedule->end_time,
                ],
                'worker' => [
                    'person_id' => $ws->person->person_id,
                    'first_name' => $ws->person->first_name,
                    'last_name' => $ws->person->last_name,
                    'specialty' => $ws->person->professional->specialty ?? null,
                    'title' => $ws->person->professional->title ?? null,
                ],
            ];
        }));
    }

    /**
     * Get available slots for a specific professional and date.
     * GET /api/worker-schedules/available-slots/{professionalId}/{date}
     */
    public function getAvailableSlots(string $professionalId, string $date)
    {
        $workerSchedules = WorkerSchedule::with('schedule')
            ->where('person_id', $professionalId)
            ->where('is_available', true)
            ->whereHas('schedule', function ($query) use ($date) {
                $query->where('date', $date);
            })
            ->get();

        return response()->json($workerSchedules->map(function ($ws) {
            return [
                'worker_schedule_id' => $ws->worker_schedule_id,
                'date' => $ws->schedule->date,
                'start_time' => $ws->schedule->start_time,
                'end_time' => $ws->schedule->end_time,
                'is_available' => $ws->is_available,
            ];
        }));
    }    
}