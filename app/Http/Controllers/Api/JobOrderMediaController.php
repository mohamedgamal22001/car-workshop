<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobOrder\StoreJobOrderMediaRequest;
use App\Models\JobOrder;
use App\Models\JobOrderMedia;

class JobOrderMediaController extends Controller
{
    /**
     * Display a listing of media for a job order.
     */
    public function index(JobOrder $jobOrder)
    {
        // To be implemented by developer
    }

    /**
     * Upload before/after photos for a job order.
     */
    public function store(StoreJobOrderMediaRequest $request, JobOrder $jobOrder)
    {
        // To be implemented by developer
    }

    /**
     * Remove a media file.
     */
    public function destroy(JobOrderMedia $media)
    {
        // To be implemented by developer
    }
}
