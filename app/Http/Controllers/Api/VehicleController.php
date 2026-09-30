<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vehicle\StoreVehicleRequest;
use App\Http\Requests\Vehicle\UpdateVehicleRequest;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // To be implemented by developer
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVehicleRequest $request)
    {
        // To be implemented by developer
    }

    /**
     * Display the specified resource.
     */
    public function show(Vehicle $vehicle)
    {
        // To be implemented by developer
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVehicleRequest $request, Vehicle $vehicle)
    {
        // To be implemented by developer
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicle $vehicle)
    {
        // To be implemented by developer
    }
}
