<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
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
    public function store(StoreCustomerRequest $request)
    {
        // To be implemented by developer
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        // To be implemented by developer
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        // To be implemented by developer
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        // To be implemented by developer
    }
}
