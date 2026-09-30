<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobOrder\AssignTechnicianRequest;
use App\Models\JobOrder;

class JobOrderAssignController extends Controller
{
    /**
     * Assign a technician to a job order.
     */
    public function __invoke(AssignTechnicianRequest $request, JobOrder $jobOrder)
    {
        // To be implemented by developer
    }
}
