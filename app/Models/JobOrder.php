<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Enums\JobOrderStatus;
use App\Traits\BelongsToWorkshop;

class JobOrder extends Model
{
    /** @use HasFactory<\Database\Factories\JobOrderFactory> */
    use HasFactory, BelongsToWorkshop;

    protected $fillable = [
        'workshop_id',
        'vehicle_id',
        'assigned_technician_id',
        'description',
        'status',
        'estimated_price',
        'expected_delivery_date',
    ];

    protected function casts(): array
    {
        return [
            'status' => JobOrderStatus::class,
            'estimated_price' => 'decimal:2',
            'expected_delivery_date' => 'datetime',
        ];
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function assignedTechnician()
    {
        return $this->belongsTo(User::class, 'assigned_technician_id');
    }

    public function media()
    {
        return $this->hasMany(JobOrderMedia::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(JobOrderStatusHistory::class);
    }
}
