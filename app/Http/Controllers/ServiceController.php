<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /*
    Recopila todos los servicios
    
    GET /api/service
    */

    public function index()
    {
        return response()->json(Service::all()); //SELECT * FROM service
    }

    /*
    Recopila el servicios solicitado
    
    GET /api/service/{id}
    */
    public function show(Service $service)
    {
        return response()->json($service);
    }

    /*
    Guarda un servicio solicitado

    POST /api/service
    */
    public function store(Request $request)
    {
        //Valida que el servicio tenga un nombre y precio existente y que cupla las restricciones definidas
        $data = $request->validate([
            'name'  => 'required|string|max:255|unique:service,name',
            'price' => 'required|numeric|gte:0', //Asegura que el precio sea un número y sea Mayor o Igual a cero
        ]);


        $service = Service::create($data);
        return response()->json($service, 201);
    }

    /*
    Modifica los datos de un servicio ya existente
    
    PUT /api/service/{id}
    */
    public function update(Request $request, Service $service)
    {
        $data = $request->validate([
            'name'  => 'sometimes|string|max:255|unique:service,name,' . $service->service_id . ',service_id', //Le digo que ignore el service_id en este caso, porque si solo edito el precio y no el nobre, saldra error
            'price' => 'sometimes|numeric|gte:0',
        ]);

        $service->update($data);
        return response()->json($service);
    }

    /*
    Elimina el registro de la base de datos

    DELETE /api/service/{id}
    */
    public function destroy(Service $service)
    {
        $service->delete();
        return response()->json(null, 204);
    }
}
