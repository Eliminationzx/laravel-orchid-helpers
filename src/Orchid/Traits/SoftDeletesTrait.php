<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes as LaravelSoftDeletes;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait SoftDeletesTrait
{
    use LaravelSoftDeletes;

    /**
     * Boot the soft deletes trait.
     *
     * @return void
     */
    public static function bootSoftDeletesTrait(): void
    {
        static::addGlobalScope('softDeletes', function (Builder $builder) {
            $model = $builder->getModel();
            $builder->whereNull($model->getDeletedAtColumn());
        });
    }

    /**
     * Force delete the model (bypass soft delete).
     *
     * @return bool|null
     */
    public function forceDelete(): ?bool
    {
        $this->forceDeleting = true;
        return parent::delete();
    }

    /**
     * Restore a soft-deleted model.
     *
     * @return bool|null
     */
    public function restore(): ?bool
    {
        // If the restoring event does not return false, we will proceed with restore
        if ($this->fireModelEvent('restoring') === false) {
            return false;
        }

        $this->{$this->getDeletedAtColumn()} = null;

        // Once we have saved the model, we will fire the "restored" event so
        // developers can hook into post-restore operations.
        $result = $this->save();

        $this->fireModelEvent('restored', false);

        return $result;
    }

    /**
     * Determine if the model is currently force deleting.
     *
     * @return bool
     */
    public function isForceDeleting(): bool
    {
        return $this->forceDeleting;
    }

    /**
     * Determine if the model has been soft-deleted.
     *
     * @return bool
     */
    public function trashed(): bool
    {
        return !is_null($this->{$this->getDeletedAtColumn()});
    }

    /**
     * Get the name of the "deleted at" column.
     *
     * @return string
     */
    public function getDeletedAtColumn(): string
    {
        return defined('static::DELETED_AT') ? constant('static::DELETED_AT') : 'deleted_at';
    }

    /**
     * Get the fully qualified "deleted at" column.
     *
     * @return string
     */
    public function getQualifiedDeletedAtColumn(): string
    {
        return $this->qualifyColumn($this->getDeletedAtColumn());
    }

    /**
     * Scope to include only trashed models.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeOnlyTrashed(Builder $query): Builder
    {
        return $query->withoutGlobalScope('softDeletes')
            ->whereNotNull($this->getDeletedAtColumn());
    }

    /**
     * Scope to include both trashed and non-trashed models.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithTrashed(Builder $query): Builder
    {
        return $query->withoutGlobalScope('softDeletes');
    }

    /**
     * Scope to include only non-trashed models.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithoutTrashed(Builder $query): Builder
    {
        return $query->whereNull($this->getDeletedAtColumn());
    }

    /**
     * Determine if the model is currently being restored.
     *
     * @return bool
     */
    public function isRestoring(): bool
    {
        return $this->restoring;
    }

    /**
     * Get the date when the model was deleted.
     *
     * @return \Illuminate\Support\Carbon|null
     */
    public function getDeletedAt(): ?\Illuminate\Support\Carbon
    {
        $deletedAt = $this->{$this->getDeletedAtColumn()};

        return $deletedAt ? \Illuminate\Support\Carbon::parse($deletedAt) : null;
    }

    /**
     * Get the time since deletion in human readable format.
     *
     * @return string|null
     */
    public function getTimeSinceDeletion(): ?string
    {
        $deletedAt = $this->getDeletedAt();

        return $deletedAt ? $deletedAt->diffForHumans() : null;
    }

    /**
     * Check if model was deleted by a specific user.
     *
     * @param int $userId
     * @return bool
     */
    public function wasDeletedBy(int $userId): bool
    {
        if ($this->trashed() && method_exists($this, 'deletedBy')) {
            return $this->deleted_by === $userId;
        }

        return false;
    }

    /**
     * Permanently delete models that were soft-deleted before a given date.
     *
     * @param \DateTimeInterface|string $date
     * @return int
     */
    public static function forceDeleteOlderThan($date): int
    {
        $date = $date instanceof \DateTimeInterface ? $date : new \DateTime($date);
        $instance = new static();
        
        $models = static::onlyTrashed()
            ->where($instance->getDeletedAtColumn(), '<', $date)
            ->get();

        $count = 0;
        foreach ($models as $model) {
            if ($model->forceDelete()) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Restore models that were soft-deleted before a given date.
     *
     * @param \DateTimeInterface|string $date
     * @return int
     */
    public static function restoreOlderThan($date): int
    {
        $date = $date instanceof \DateTimeInterface ? $date : new \DateTime($date);
        $instance = new static();
        
        $models = static::onlyTrashed()
            ->where($instance->getDeletedAtColumn(), '<', $date)
            ->get();

        $count = 0;
        foreach ($models as $model) {
            if ($model->restore()) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Get the count of trashed models.
     *
     * @return int
     */
    public static function countTrashed(): int
    {
        return static::onlyTrashed()->count();
    }

    /**
     * Get the count of non-trashed models.
     *
     * @return int
     */
    public static function countWithoutTrashed(): int
    {
        return static::withoutTrashed()->count();
    }

    /**
     * Get the percentage of trashed models.
     *
     * @return float
     */
    public static function getTrashedPercentage(): float
    {
        $total = static::withTrashed()->count();
        
        if ($total === 0) {
            return 0.0;
        }

        $trashed = static::countTrashed();

        return ($trashed / $total) * 100;
    }

    /**
     * Check if model can be restored.
     *
     * @return bool
     */
    public function canRestore(): bool
    {
        return $this->trashed();
    }

    /**
     * Check if model can be force deleted.
     *
     * @return bool
     */
    public function canForceDelete(): bool
    {
        return $this->trashed();
    }

    /**
     * Get restoration eligibility message.
     *
     * @return string
     */
    public function getRestorationMessage(): string
    {
        if (!$this->trashed()) {
            return 'Model is not deleted.';
        }

        $deletedAt = $this->getDeletedAt();
        $daysAgo = $deletedAt ? $deletedAt->diffInDays() : 0;

        if ($daysAgo > 30) {
            return "Deleted {$daysAgo} days ago. Consider permanent deletion.";
        }

        return "Can be restored. Deleted {$daysAgo} days ago.";
    }

    /**
     * Register a restoring model event with the dispatcher.
     *
     * @param \Closure|string $callback
     * @return void
     */
    public static function restoring($callback): void
    {
        static::registerModelEvent('restoring', $callback);
    }

    /**
     * Register a restored model event with the dispatcher.
     *
     * @param \Closure|string $callback
     * @return void
     */
    public static function restored($callback): void
    {
        static::registerModelEvent('restored', $callback);
    }

    /**
     * Register a force deleting model event with the dispatcher.
     *
     * @param \Closure|string $callback
     * @return void
     */
    public static function forceDeleting($callback): void
    {
        static::registerModelEvent('forceDeleting', $callback);
    }

    /**
     * Register a force deleted model event with the dispatcher.
     *
     * @param \Closure|string $callback
     * @return void
     */
    public static function forceDeleted($callback): void
    {
        static::registerModelEvent('forceDeleted', $callback);
    }
}
