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
        'file_path',
        'media_type',
        'file_name',
    ];

    public function jobOrder()
    {
        return $this->belongsTo(JobOrder::class);
    }
}
