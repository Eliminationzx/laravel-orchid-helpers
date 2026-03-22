<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait AuditableTrait
{
    /**
     * Boot the auditable trait.
     *
     * @return void
     */
    public static function bootAuditableTrait(): void
    {
        static::created(function (Model $model) {
            $model->logAuditEvent('created', $model->getAttributes());
        });

        static::updated(function (Model $model) {
            $model->logAuditEvent('updated', $model->getChanges(), $model->getOriginal());
        });

        static::deleted(function (Model $model) {
            $model->logAuditEvent('deleted', $model->getAttributes());
        });

        static::restored(function (Model $model) {
            $model->logAuditEvent('restored', $model->getAttributes());
        });
    }

    /**
     * Log an audit event.
     *
     * @param string $event
     * @param array $newData
     * @param array|null $oldData
     * @return void
     */
    public function logAuditEvent(string $event, array $newData = [], ?array $oldData = null): void
    {
        $auditData = [
            'event' => $event,
            'model' => get_class($this),
            'model_id' => $this->getKey(),
            'new_data' => $this->filterAuditData($newData),
            'old_data' => $oldData ? $this->filterAuditData($oldData) : null,
            'user_id' => Auth::id(),
            'user_type' => Auth::user() ? get_class(Auth::user()) : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'timestamp' => now()->toISOString(),
        ];

        $this->saveAuditRecord($auditData);
        $this->logToChannel($auditData);
    }

    /**
     * Filter sensitive data from audit logs.
     *
     * @param array $data
     * @return array
     */
    protected function filterAuditData(array $data): array
    {
        $sensitiveFields = $this->getSensitiveFields();

        foreach ($sensitiveFields as $field) {
            if (array_key_exists($field, $data)) {
                $data[$field] = '[FILTERED]';
            }
        }

        return $data;
    }

    /**
     * Get sensitive fields that should be filtered.
     *
     * @return array
     */
    protected function getSensitiveFields(): array
    {
        if (property_exists($this, 'sensitiveFields')) {
            return $this->sensitiveFields;
        }

        return [
            'password',
            'password_confirmation',
            'token',
            'api_token',
            'secret',
            'private_key',
            'credit_card',
            'ssn',
            'social_security_number',
        ];
    }

    /**
     * Save audit record to database.
     *
     * @param array $auditData
     * @return void
     */
    protected function saveAuditRecord(array $auditData): void
    {
        $auditModel = $this->getAuditModel();

        if ($auditModel) {
            $auditModel::create($auditData);
        }
    }

    /**
     * Get the audit model class.
     *
     * @return string|null
     */
    protected function getAuditModel(): ?string
    {
        if (property_exists($this, 'auditModel')) {
            return $this->auditModel;
        }

        // Check for common audit model names
        $commonModels = [
            'App\Models\Audit',
            'App\Models\AuditLog',
            'App\Models\ActivityLog',
        ];

        foreach ($commonModels as $model) {
            if (class_exists($model)) {
                return $model;
            }
        }

        return null;
    }

    /**
     * Log audit event to log channel.
     *
     * @param array $auditData
     * @return void
     */
    protected function logToChannel(array $auditData): void
    {
        $channel = config('audit.log_channel', 'daily');
        $level = config('audit.log_level', 'info');

        Log::channel($channel)->log($level, 'Audit Event', $auditData);
    }

    /**
     * Get audit history for this model.
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAuditHistory(int $limit = 50)
    {
        $auditModel = $this->getAuditModel();

        if (!$auditModel) {
            return collect();
        }

        return $auditModel::where('model', get_class($this))
            ->where('model_id', $this->getKey())
            ->orderBy('timestamp', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get changes between two versions.
     *
     * @param array $oldData
     * @param array $newData
     * @return array
     */
    public function getAuditChanges(array $oldData, array $newData): array
    {
        $changes = [];

        foreach ($newData as $key => $value) {
            if (!array_key_exists($key, $oldData) || $oldData[$key] != $value) {
                $changes[$key] = [
                    'old' => $oldData[$key] ?? null,
                    'new' => $value,
                ];
            }
        }

        // Check for deleted fields
        foreach ($oldData as $key => $value) {
            if (!array_key_exists($key, $newData)) {
                $changes[$key] = [
                    'old' => $value,
                    'new' => null,
                ];
            }
        }

        return $changes;
    }

    /**
     * Log a custom audit event.
     *
     * @param string $event
     * @param array $data
     * @param string|null $description
     * @return void
     */
    public function logCustomEvent(string $event, array $data = [], ?string $description = null): void
    {
        $auditData = [
            'event' => $event,
            'model' => get_class($this),
            'model_id' => $this->getKey(),
            'new_data' => $data,
            'description' => $description,
            'user_id' => Auth::id(),
            'user_type' => Auth::user() ? get_class(Auth::user()) : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString(),
        ];

        $this->saveAuditRecord($auditData);
    }

    /**
     * Get who performed an action.
     *
     * @param string $event
     * @param string|null $timestamp
     * @return array|null
     */
    public function getAuditActor(string $event, ?string $timestamp = null): ?array
    {
        $auditModel = $this->getAuditModel();

        if (!$auditModel) {
            return null;
        }

        $query = $auditModel::where('model', get_class($this))
            ->where('model_id', $this->getKey())
            ->where('event', $event);

        if ($timestamp) {
            $query->where('timestamp', $timestamp);
        }

        $audit = $query->first();

        if (!$audit) {
            return null;
        }

        return [
            'user_id' => $audit->user_id,
            'user_type' => $audit->user_type,
            'ip_address' => $audit->ip_address,
            'timestamp' => $audit->timestamp,
        ];
    }

    /**
     * Check if model has been modified by a specific user.
     *
     * @param int $userId
     * @param string|null $event
     * @return bool
     */
    public function wasModifiedBy(int $userId, ?string $event = null): bool
    {
        $auditModel = $this->getAuditModel();

        if (!$auditModel) {
            return false;
        }

        $query = $auditModel::where('model', get_class($this))
            ->where('model_id', $this->getKey())
            ->where('user_id', $userId);

        if ($event) {
            $query->where('event', $event);
        }

        return $query->exists();
    }

    /**
     * Get last modification timestamp.
     *
     * @return string|null
     */
    public function getLastModifiedAt(): ?string
    {
        $auditModel = $this->getAuditModel();

        if (!$auditModel) {
            return null;
        }

        $audit = $auditModel::where('model', get_class($this))
            ->where('model_id', $this->getKey())
            ->whereIn('event', ['created', 'updated'])
            ->orderBy('timestamp', 'desc')
            ->first();

        return $audit ? $audit->timestamp : null;
    }

    /**
     * Get creation timestamp from audit log.
     *
     * @return string|null
     */
    public function getCreatedAtFromAudit(): ?string
    {
        $auditModel = $this->getAuditModel();

        if (!$auditModel) {
            return null;
        }

        $audit = $auditModel::where('model', get_class($this))
            ->where('model_id', $this->getKey())
            ->where('event', 'created')
            ->first();

        return $audit ? $audit->timestamp : null;
    }

    /**
     * Enable/disable audit logging.
     *
     * @param bool $enabled
     * @return void
     */
    public static function setAuditLogging(bool $enabled = true): void
    {
        if ($enabled) {
            static::bootAuditableTrait();
        } else {
            // Remove event listeners
            static::flushEventListeners();
            static::boot();
        }
    }
}
