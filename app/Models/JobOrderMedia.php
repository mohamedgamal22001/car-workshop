<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOrderMedia extends Model
{
    /** @use HasFactory<\Database\Factories\JobOrderMediaFactory> */
    use HasFactory;

    protected $fillable = [
        'job_order_id',
        'type',
        'path',
        'uploaded_by',
    ];

    public function jobOrder()
    {
        return $this->belongsTo(JobOrder::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
