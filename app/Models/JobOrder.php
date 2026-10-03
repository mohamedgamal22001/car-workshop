<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOrder extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_WAITING_PARTS = 'waiting_parts';
    const STATUS_READY = 'ready';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'workshop_id',
        'vehicle_id',
        'assigned_technician_id',
        'description',
        'status',
        'estimated_price',
        'labor_cost',
        'expected_delivery_date',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'estimated_price' => 'decimal:2',
            'labor_cost' => 'decimal:2',
            'expected_delivery_date' => 'datetime',
        ];
    }

    protected $appends = [
        'is_overdue',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('workshop', function ($builder) {
            if (auth()->check() && auth()->user()->workshop_id) {
                $builder->where('job_orders.workshop_id', auth()->user()->workshop_id);
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

    public function getIsOverdueAttribute(): bool
    {
        if (!$this->expected_delivery_date) {
            return false;
        }

        return !in_array($this->status, [self::STATUS_DELIVERED, self::STATUS_CANCELLED], true)
            && now()->gt($this->expected_delivery_date);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function assignedTechnician()
    {
        return $this->belongsTo(User::class, 'assigned_technician_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
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
