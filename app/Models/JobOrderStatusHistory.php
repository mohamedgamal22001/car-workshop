<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOrderStatusHistory extends Model
{
    /** @use HasFactory<\Database\Factories\JobOrderStatusHistoryFactory> */
    use HasFactory;

    protected $fillable = [
        'job_order_id',
        'changed_by_user_id',
        'from_status',
        'to_status',
        'notes',
    ];

    public function jobOrder()
    {
        return $this->belongsTo(JobOrder::class);
    }

    public function changedByUser()
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}
