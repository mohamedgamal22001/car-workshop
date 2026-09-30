<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    /** @use HasFactory<\Database\Factories\VehicleFactory> */
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'make',
        'model',
        'plate_number',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function jobOrders()
    {
        return $this->hasMany(JobOrder::class);
    }
}
