<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait CacheableTrait
{
    /**
     * Get cache key for the model.
     *
     * @param string|null $suffix
     * @return string
     */
    public function getCacheKey(?string $suffix = null): string
    {
        $key = strtolower(class_basename($this)) . ':' . $this->getKey();
        
        if ($suffix) {
            $key .= ':' . $suffix;
        }
        
        return $key;
    }

    /**
     * Get cache TTL (time to live) in seconds.
     *
     * @return int
     */
    protected function getCacheTtl(): int
    {
        if (property_exists($this, 'cacheTtl')) {
            return $this->cacheTtl;
        }
        
        return config('cache.ttl', 3600); // 1 hour default
    }

    /**
     * Get cached value or store default.
     *
     * @param string $key
     * @param \Closure $callback
     * @param int|null $ttl
     * @return mixed
     */
    public static function remember(string $key, \Closure $callback, ?int $ttl = null)
    {
        $ttl = $ttl ?? (new static())->getCacheTtl();
        
        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Get cached value or store default for this model instance.
     *
     * @param string $suffix
     * @param \Closure $callback
     * @param int|null $ttl
     * @return mixed
     */
    public function rememberInstance(string $suffix, \Closure $callback, ?int $ttl = null)
    {
        $key = $this->getCacheKey($suffix);
        $ttl = $ttl ?? $this->getCacheTtl();
        
        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Forget cache for this model instance.
     *
     * @param string|null $suffix
     * @return bool
     */
    public function forgetInstance(?string $suffix = null): bool
    {
        $key = $this->getCacheKey($suffix);
        
        return Cache::forget($key);
    }

    /**
     * Forget all cache for this model type.
     *
     * @return void
     */
    public static function forgetAll(): void
    {
        $pattern = strtolower(class_basename(static::class)) . ':*';
        
        // Note: This works with Redis, may need adaptation for other cache drivers
        if (method_exists(Cache::store(), 'tags')) {
            Cache::tags(static::class)->flush();
        } else {
            // Fallback: clear entire cache (use with caution)
            Cache::flush();
        }
    }

    /**
     * Get model from cache or database.
     *
     * @param int|string $id
     * @return Model|null
     */
    public static function findCached($id): ?Model
    {
        $instance = new static();
        $key = $instance->getCacheKeyForId($id);
        
        return static::remember($key, function () use ($id) {
            return static::find($id);
        });
    }

    /**
     * Get cache key for model ID.
     *
     * @param int|string $id
     * @return string
     */
    protected function getCacheKeyForId($id): string
    {
        return strtolower(class_basename($this)) . ':' . $id;
    }

    /**
     * Get all models with caching.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function allCached()
    {
        $instance = new static();
        $key = strtolower(class_basename($instance)) . ':all';
        
        return static::remember($key, function () {
            return static::all();
        });
    }

    /**
     * Cache query results.
     *
     * @param \Closure $queryCallback
     * @param string $cacheKey
     * @param int|null $ttl
     * @return mixed
     */
    public static function cacheQuery(\Closure $queryCallback, string $cacheKey, ?int $ttl = null)
    {
        $instance = new static();
        $ttl = $ttl ?? $instance->getCacheTtl();
        
        return Cache::remember($cacheKey, $ttl, $queryCallback);
    }

    /**
     * Increment cache counter.
     *
     * @param string $suffix
     * @param int $value
     * @return int
     */
    public function incrementCache(string $suffix, int $value = 1): int
    {
        $key = $this->getCacheKey($suffix);
        
        return Cache::increment($key, $value);
    }

    /**
     * Decrement cache counter.
     *
     * @param string $suffix
     * @param int $value
     * @return int
     */
    public function decrementCache(string $suffix, int $value = 1): int
    {
        $key = $this->getCacheKey($suffix);
        
        return Cache::decrement($key, $value);
    }

    /**
     * Get cache tags for this model.
     *
     * @return array
     */
    protected function getCacheTags(): array
    {
        if (property_exists($this, 'cacheTags')) {
            return $this->cacheTags;
        }
        
        return [static::class];
    }

    /**
     * Cache with tags.
     *
     * @param string $key
     * @param \Closure $callback
     * @param int|null $ttl
     * @return mixed
     */
    public static function rememberWithTags(string $key, \Closure $callback, ?int $ttl = null)
    {
        $instance = new static();
        $ttl = $ttl ?? $instance->getCacheTtl();
        $tags = $instance->getCacheTags();
        
        if (method_exists(Cache::store(), 'tags')) {
            return Cache::tags($tags)->remember($key, $ttl, $callback);
        }
        
        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Clear cache with tags.
     *
     * @return void
     */
    public static function clearTaggedCache(): void
    {
        $instance = new static();
        $tags = $instance->getCacheTags();
        
        if (method_exists(Cache::store(), 'tags')) {
            Cache::tags($tags)->flush();
        }
    }

    /**
     * Cache model relationships.
     *
     * @param string $relation
     * @param int|null $ttl
     * @return mixed
     */
    public function getCachedRelation(string $relation, ?int $ttl = null)
    {
        $key = $this->getCacheKey('relation:' . $relation);
        $ttl = $ttl ?? $this->getCacheTtl();
        
        return Cache::remember($key, $ttl, function () use ($relation) {
            return $this->$relation;
        });
    }

    /**
     * Cache model count.
     *
     * @param string|null $column
     * @param mixed $value
     * @param int|null $ttl
     * @return int
     */
    public static function countCached(?string $column = null, $value = null, ?int $ttl = null): int
    {
        $instance = new static();
        $key = strtolower(class_basename($instance)) . ':count';
        
        if ($column) {
            $key .= ':' . $column . ':' . $value;
        }
        
        $ttl = $ttl ?? $instance->getCacheTtl();
        
        return (int) Cache::remember($key, $ttl, function () use ($column, $value) {
            $query = static::query();
            
            if ($column && $value !== null) {
                $query->where($column, $value);
            }
            
            return $query->count();
        });
    }

    /**
     * Cache model aggregation.
     *
     * @param string $function
     * @param string $column
     * @param int|null $ttl
     * @return mixed
     */
    public static function aggregateCached(string $function, string $column, ?int $ttl = null)
    {
        $instance = new static();
        $key = strtolower(class_basename($instance)) . ':aggregate:' . $function . ':' . $column;
        $ttl = $ttl ?? $instance->getCacheTtl();
        
        return Cache::remember($key, $ttl, function () use ($function, $column) {
            return static::query()->$function($column);
        });
    }

    /**
     * Check if cache exists.
     *
     * @param string $suffix
     * @return bool
     */
    public function hasCache(string $suffix): bool
    {
        $key = $this->getCacheKey($suffix);
        
        return Cache::has($key);
    }

    /**
     * Get cache value.
     *
     * @param string $suffix
     * @param mixed $default
     * @return mixed
     */
    public function getCache(string $suffix, $default = null)
    {
        $key = $this->getCacheKey($suffix);
        
        return Cache::get($key, $default);
    }

    /**
     * Set cache value.
     *
     * @param string $suffix
     * @param mixed $value
     * @param int|null $ttl
     * @return bool
     */
    public function setCache(string $suffix, $value, ?int $ttl = null): bool
    {
        $key = $this->getCacheKey($suffix);
        $ttl = $ttl ?? $this->getCacheTtl();
        
        return Cache::put($key, $value, $ttl);
    }

    /**
     * Boot the cacheable trait.
     *
     * @return void
     */
    public static function bootCacheableTrait(): void
    {
        static::saved(function (Model $model) {
            $model->clearModelCache();
        });

        static::deleted(function (Model $model) {
            $model->clearModelCache();
        });
    }

    /**
     * Clear cache for this model.
     *
     * @return void
     */
    public function clearModelCache(): void
    {
        $this->forgetInstance();
        static::clearTaggedCache();
    }
}
