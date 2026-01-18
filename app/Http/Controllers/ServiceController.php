<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    // GET /api/service
    public function index()
    {
        return response()->json(Service::all());
    }

    // GET /api/service/{id}
    public function show(Service $service)
    {
        return response()->json($service);
    }

    // POST /api/service
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255|unique:service,name',
            'price' => 'required|numeric|gte:0',
        ]);

        $service = Service::create($data);
        return response()->json($service, 201);
    }

    // PUT /api/service/{id}
    public function update(Request $request, Service $service)
    {
        $data = $request->validate([
            'name'  => 'sometimes|string|max:255|unique:service,name,' . $service->service_id . ',service_id',
            'price' => 'sometimes|numeric|gte:0',
        ]);

        $service->update($data);
        return response()->json($service);
    }

    // DELETE /api/service/{id}
    public function destroy(Service $service)
    {
        $service->delete();
        return response()->json(null, 204);
    }
}
