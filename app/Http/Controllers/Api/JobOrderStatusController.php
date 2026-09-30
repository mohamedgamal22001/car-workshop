<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobOrder\ChangeStatusRequest;
use App\Models\JobOrder;

class JobOrderStatusController extends Controller
{
    /**
     * Update the workflow status of a job order.
     */
    public function update(ChangeStatusRequest $request, JobOrder $jobOrder)
    {
        // To be implemented by developer
    }
}
