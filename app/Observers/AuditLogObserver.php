<?php
// app/Observers/AuditLogObserver.php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

/**
 * Generic Eloquent observer — attach this to any model you want automatically
 * tracked in the audit_logs table.
 *
 * Registration (in AppServiceProvider::boot):
 *
 *   BusinessEntry::observe(AuditLogObserver::class);
 *   BplsPayment::observe(AuditLogObserver::class);
 *   OrAssignment::observe(AuditLogObserver::class);
 *
 * Each observed model MAY define:
 *   protected string $auditModule = 'BPLS';       ← module label (default: model class short name)
 *   protected array  $auditHidden = ['password'];  ← fields to hide in old/new values
 */
class AuditLogObserver
{
    // ── Lifecycle hooks ───────────────────────────────────────────────────────

    public function created(Model $model): void
    {
        $this->log(
            $model,
            'created',
            $this->label($model) . ' was created.',
            null,
            $this->sanitize($model->getAttributes(), $model)
        );
    }

    public function updated(Model $model): void
    {
        $dirty = $model->getDirty();

        if (empty($dirty)) {
            return;
        }

        $old = array_intersect_key($model->getOriginal(), $dirty);
        $new = $dirty;

        $this->log(
            $model,
            'updated',
            $this->label($model) . ' was updated.',
            $this->sanitize($old, $model),
            $this->sanitize($new, $model)
        );
    }

    public function deleted(Model $model): void
    {
        $this->log(
            $model,
            'deleted',
            $this->label($model) . ' was deleted.',
            $this->sanitize($model->getAttributes(), $model),
            null
        );
    }

    public function restored(Model $model): void
    {
        $this->log(
            $model,
            'updated',
            $this->label($model) . ' was restored from soft-delete.',
            null,
            null
        );
    }

    // ── Internal helpers ──────────────────────────────────────────────────────

    private function log(
        Model $model,
        string $action,
        string $description,
        ?array $oldValues,
        ?array $newValues
    ): void {
        try {
            // Positional arguments — compatible with PHP 7.4+
            AuditLog::record(
                $this->module($model),
                $action,
                $description,
                $model,
                [
                    'old_values' => $oldValues,
                    'new_values' => $newValues,
                ]
            );
        } catch (\Throwable $e) {
            // Never let audit logging crash the main request
            \Log::error('AuditLogObserver error: ' . $e->getMessage());
        }
    }

    /** Derive a human-readable label from the model. */
    private function label(Model $model): string
    {
        $name = class_basename($model);

        foreach (['business_name', 'name', 'title'] as $field) {
            if (!empty($model->{$field})) {
                return $name . ' "' . $model->{$field} . '"';
            }
        }

        return $name . ' #' . $model->id;
    }

    /** Determine the module name from an optional property or the class name. */
    private function module(Model $model): string
    {
        return property_exists($model, 'auditModule')
            ? $model->auditModule
            : class_basename($model);
    }

    /** Strip sensitive fields before storing values. */
    private function sanitize(array $values, Model $model): array
    {
        $hidden = property_exists($model, 'auditHidden')
            ? $model->auditHidden
            : ['password', 'remember_token'];

        return array_diff_key($values, array_flip($hidden));
    }
}