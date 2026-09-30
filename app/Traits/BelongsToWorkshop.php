<?php

namespace App\Traits;

use App\Models\Scopes\WorkshopScope;
use App\Models\Workshop;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait BelongsToWorkshop
{
    /**
     * Boot the trait.
     */
    protected static function bootBelongsToWorkshop(): void
    {
        static::addGlobalScope(new WorkshopScope());

        static::creating(function ($model) {
            if (Auth::check() && empty($model->workshop_id) && Auth::user()->workshop_id) {
                $model->workshop_id = Auth::user()->workshop_id;
            }
        });
    }

    /**
     * Relationship to the workshop.
     */
    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }
}
