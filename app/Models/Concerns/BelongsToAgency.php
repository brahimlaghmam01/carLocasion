<?php

namespace App\Models\Concerns;

use App\Models\Agency;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * Automatically scopes a model's queries to the authenticated Agency Admin's
 * agency and assigns the agency_id on creation. Super Admins, legacy Admins,
 * clients and guests are never restricted, so existing behaviour is preserved.
 */
trait BelongsToAgency
{
    public static function bootBelongsToAgency(): void
    {
        static::addGlobalScope('agency', function (Builder $builder) {
            $user = Auth::user();

            if ($user && method_exists($user, 'isAgencyAdmin') && $user->isAgencyAdmin() && $user->agency_id) {
                $builder->where($builder->getModel()->getTable() . '.agency_id', $user->agency_id);
            }
        });

        static::creating(function ($model) {
            if (! empty($model->agency_id)) {
                return;
            }

            $user = Auth::user();

            if ($user && method_exists($user, 'isAgencyAdmin') && $user->isAgencyAdmin() && $user->agency_id) {
                $model->agency_id = $user->agency_id;
            }
        });
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }
}
