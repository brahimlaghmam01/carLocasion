<?php

namespace App\Models\Concerns;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

/**
 * Records create/update/delete events for the model into the activity_logs
 * table. Logging only happens for authenticated users, so seeders, guest
 * traffic and background jobs do not generate noise.
 */
trait LogsActivity
{
    public static function bootLogsActivity(): void
    {
        static::created(fn ($model) => $model->logActivity('created'));
        static::updated(fn ($model) => $model->logActivity($model->resolveActivityAction()));
        static::deleted(fn ($model) => $model->logActivity('deleted'));
    }

    public function logActivity(string $action): void
    {
        if (! Auth::check()) {
            return;
        }

        $label = class_basename($this);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'agency_id' => $this->agency_id ?? Auth::user()->agency_id,
            'action' => $action,
            'model' => static::class,
            'record_id' => $this->getKey(),
            'description' => ucfirst($action) . " {$label} #{$this->getKey()}",
        ]);
    }

    /**
     * Resolve a more specific action for status-driven models on update.
     */
    protected function resolveActivityAction(): string
    {
        if ($this->isDirty('status') || $this->wasChanged('status')) {
            $status = $this->status instanceof \BackedEnum ? $this->status->value : $this->status;

            return match ((string) $status) {
                'confirmed', 'active' => 'approved',
                'cancelled' => 'cancelled',
                default => 'updated',
            };
        }

        return 'updated';
    }
}
