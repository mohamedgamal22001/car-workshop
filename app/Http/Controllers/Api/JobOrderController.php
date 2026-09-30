<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobOrder\StoreJobOrderRequest;
use App\Http\Requests\JobOrder\UpdateJobOrderRequest;
use App\Models\JobOrder;
use Illuminate\Http\Request;

class JobOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // To be implemented by developer
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJobOrderRequest $request)
    {
        // To be implemented by developer
    }

    /**
     * Display the specified resource.
     */
    public function show(JobOrder $jobOrder)
    {
        // To be implemented by developer
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJobOrderRequest $request, JobOrder $jobOrder)
    {
        // To be implemented by developer
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobOrder $jobOrder)
    {
        // To be implemented by developer
    }
}
