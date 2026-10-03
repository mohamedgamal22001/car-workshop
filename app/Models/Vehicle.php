<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'workshop_id',
        'customer_id',
        'make',
        'model',
        'plate_number',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('workshop', function ($builder) {
            if (auth()->check() && auth()->user()->workshop_id) {
                $builder->where('vehicles.workshop_id', auth()->user()->workshop_id);
            }
        });

        static::creating(function ($model) {
            if (auth()->check() && empty($model->workshop_id) && auth()->user()->workshop_id) {
                $model->workshop_id = auth()->user()->workshop_id;
            }
        });
    }

    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function jobOrders()
    {
        return $this->hasMany(JobOrder::class);
    }
}
