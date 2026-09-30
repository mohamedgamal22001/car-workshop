<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToWorkshop;

class Customer extends Model
{
    /** @use HasFactory<\Database\Factories\CustomerFactory> */
    use HasFactory, BelongsToWorkshop;

    protected $fillable = [
        'workshop_id',
        'name',
        'phone',
    ];

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }
}
