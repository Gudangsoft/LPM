<?php

namespace App\Models\Concerns;

use App\Models\AuditTrail;
use Illuminate\Database\Eloquent\Model;

/**
 * Writes an AuditTrail row on create / update / delete. Workflow methods
 * (verify, approve, close, ...) call AuditTrail::log() directly for named events.
 */
trait RecordsAuditTrail
{
    public static function bootRecordsAuditTrail(): void
    {
        static::created(function (Model $model) {
            AuditTrail::log($model, 'created');
        });

        static::updated(function (Model $model) {
            $changes = collect($model->getChanges())
                ->except(['updated_at'])
                ->mapWithKeys(fn ($new, $key) => [$key => [$model->getOriginal($key), $new]])
                ->all();

            if ($changes === []) {
                return;
            }

            AuditTrail::log($model, 'updated', null, $changes);
        });

        static::deleted(function (Model $model) {
            AuditTrail::log($model, 'deleted');
        });
    }
}
