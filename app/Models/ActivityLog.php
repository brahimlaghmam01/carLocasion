<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'agency_id',
        'action',
        'model',
        'record_id',
        'description',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the agency the action was scoped to.
     */
    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }
}
