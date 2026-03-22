<?php

declare(strict_types=1);

namespace Orchid\Helpers\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class CacheService
{
    public static function make(): static
    {
        return new static();
    }

    /**
     * Get item from cache
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return Cache::get($key, $default);
    }

    /**
     * Store item in cache
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  \DateTimeInterface|\DateInterval|int|null  $ttl
     * @return bool
     */
    public function put(string $key, $value, $ttl = null): bool
    {
        return Cache::put($key, $value, $ttl);
    }

    /**
     * Store item in cache forever
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return bool
     */
    public function forever(string $key, $value): bool
    {
        return Cache::forever($key, $value);
    }

    /**
     * Store item in cache if it doesn't exist
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  \DateTimeInterface|\DateInterval|int|null  $ttl
     * @return bool
     */
    public function add(string $key, $value, $ttl = null): bool
    {
        return Cache::add($key, $value, $ttl);
    }

    /**
     * Increment cache value
     *
     * @param  string  $key
     * @param  int  $value
     * @return int|bool
     */
    public function increment(string $key, int $value = 1)
    {
        return Cache::increment($key, $value);
    }

    /**
     * Decrement cache value
     *
     * @param  string  $key
     * @param  int  $value
     * @return int|bool
     */
    public function decrement(string $key, int $value = 1)
    {
        return Cache::decrement($key, $value);
    }

    /**
     * Remove item from cache
     *
     * @param  string  $key
     * @return bool
     */
    public function forget(string $key): bool
    {
        return Cache::forget($key);
    }

    /**
     * Clear entire cache
     *
     * @return bool
     */
    public function clear(): bool
    {
        return Cache::clear();
    }

    /**
     * Get multiple items from cache
     *
     * @param  array  $keys
     * @param  mixed  $default
     * @return array
     */
    public function many(array $keys, $default = null): array
    {
        return Cache::many($keys);
    }

    /**
     * Store multiple items in cache
     *
     * @param  array  $values
     * @param  \DateTimeInterface|\DateInterval|int|null  $ttl
     * @return bool
     */
    public function putMany(array $values, $ttl = null): bool
    {
        return Cache::putMany($values, $ttl);
    }

    /**
     * Get item from cache or store default value
     *
     * @param  string  $key
     * @param  \DateTimeInterface|\DateInterval|int|null  $ttl
     * @param  callable  $callback
     * @return mixed
     */
    public function remember(string $key, $ttl, callable $callback)
    {
        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Get item from cache or store default value forever
     *
     * @param  string  $key
     * @param  callable  $callback
     * @return mixed
     */
    public function rememberForever(string $key, callable $callback)
    {
        return Cache::rememberForever($key, $callback);
    }

    /**
     * Get item from cache or store default value and tag it
     *
     * @param  array  $tags
     * @param  string  $key
     * @param  \DateTimeInterface|\DateInterval|int|null  $ttl
     * @param  callable  $callback
     * @return mixed
     */
    public function taggedRemember(array $tags, string $key, $ttl, callable $callback)
    {
        return Cache::tags($tags)->remember($key, $ttl, $callback);
    }

    /**
     * Check if item exists in cache
     *
     * @param  string  $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return Cache::has($key);
    }

    /**
     * Get cache key with prefix
     *
     * @param  string  $key
     * @return string
     */
    public function getKey(string $key): string
    {
        return Cache::getPrefix() . $key;
    }

    /**
     * Get cache store instance
     *
     * @param  string|null  $store
     * @return \Illuminate\Contracts\Cache\Repository
     */
    public function store(?string $store = null)
    {
        return Cache::store($store);
    }

    /**
     * Get cache driver name
     *
     * @return string
     */
    public function getDriver(): string
    {
        return config('cache.default');
    }

    /**
     * Get cache configuration
     *
     * @param  string|null  $store
     * @return array
     */
    public function getConfig(?string $store = null): array
    {
        $store = $store ?? config('cache.default');
        return config("cache.stores.{$store}", []);
    }

    /**
     * Get cache statistics (Redis only)
     *
     * @return array|null
     */
    public function getStats(): ?array
    {
        if ($this->getDriver() !== 'redis') {
            return null;
        }
        
        try {
            $redis = Redis::connection('cache');
            $info = $redis->info();
            
            return [
                'used_memory' => $info['used_memory'] ?? null,
                'used_memory_human' => $info['used_memory_human'] ?? null,
                'connected_clients' => $info['connected_clients'] ?? null,
                'total_commands_processed' => $info['total_commands_processed'] ?? null,
                'keyspace_hits' => $info['keyspace_hits'] ?? null,
                'keyspace_misses' => $info['keyspace_misses'] ?? null,
                'hit_rate' => isset($info['keyspace_hits'], $info['keyspace_misses']) 
                    ? ($info['keyspace_hits'] / ($info['keyspace_hits'] + $info['keyspace_misses'])) * 100 
                    : null,
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get cache keys by pattern
     *
     * @param  string  $pattern
     * @return array
     */
    public function getKeys(string $pattern = '*'): array
    {
        if ($this->getDriver() !== 'redis') {
            return [];
        }
        
        try {
            $redis = Redis::connection('cache');
            return $redis->keys($pattern);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get cache key count
     *
     * @return int|null
     */
    public function getKeyCount(): ?int
    {
        if ($this->getDriver() !== 'redis') {
            return null;
        }
        
        try {
            $redis = Redis::connection('cache');
            return count($redis->keys('*'));
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Clear cache by tag
     *
     * @param  array|string  $tags
     * @return bool
     */
    public function clearTag($tags): bool
    {
        try {
            Cache::tags($tags)->flush();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Clear cache by pattern
     *
     * @param  string  $pattern
     * @return int
     */
    public function clearByPattern(string $pattern): int
    {
        if ($this->getDriver() !== 'redis') {
            return 0;
        }
        
        try {
            $redis = Redis::connection('cache');
            $keys = $redis->keys($pattern);
            $count = count($keys);
            
            if ($count > 0) {
                $redis->del($keys);
            }
            
            return $count;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get cache item size (Redis only)
     *
     * @param  string  $key
     * @return int|null
     */
    public function getKeySize(string $key): ?int
    {
        if ($this->getDriver() !== 'redis') {
            return null;
        }
        
        try {
            $redis = Redis::connection('cache');
            return strlen(serialize($redis->get($key)));
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get cache TTL
     *
     * @param  string  $key
     * @return int|null
     */
    public function getTtl(string $key): ?int
    {
        if ($this->getDriver() !== 'redis') {
            return null;
        }
        
        try {
            $redis = Redis::connection('cache');
            return $redis->ttl($key);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Set cache TTL
     *
     * @param  string  $key
     * @param  int  $ttl
     * @return bool
     */
    public function setTtl(string $key, int $ttl): bool
    {
        if ($this->getDriver() !== 'redis') {
            return false;
        }
        
        try {
            $redis = Redis::connection('cache');
            return $redis->expire($key, $ttl) > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Cache lock
     *
     * @param  string  $key
     * @param  int  $seconds
     * @param  string|null  $owner
     * @return \Illuminate\Contracts\Cache\Lock
     */
    public function lock(string $key, int $seconds = 0, ?string $owner = null)
    {
        return Cache::lock($key, $seconds, $owner);
    }

    /**
     * Cache with lock
     *
     * @param  string  $key
     * @param  \DateTimeInterface|\DateInterval|int|null  $ttl
     * @param  callable  $callback
     * @param  int  $lockSeconds
     * @return mixed
     */
    public function rememberWithLock(string $key, $ttl, callable $callback, int $lockSeconds = 10)
    {
        $lock = $this->lock($key . '_lock', $lockSeconds);
        
        try {
            if ($lock->get()) {
                return $this->remember($key, $ttl, $callback);
            }
            
            // Wait for lock and retry
            $lock->block(5);
            return $this->remember($key, $ttl, $callback);
        } finally {
            $lock->release();
        }
    }

    /**
     * Generate cache key from parameters
     *
     * @param  string  $prefix
     * @param  array  $parameters
     * @return string
     */
    public function generateKey(string $prefix, array $parameters = []): string
    {
        if (empty($parameters)) {
            return $prefix;
        }
        
        ksort($parameters);
        $hash = md5(serialize($parameters));
        
        return $prefix . '_' . $hash;
    }

    /**
     * Cache paginated results
     *
     * @param  string  $key
     * @param  \DateTimeInterface|\DateInterval|int|null  $ttl
     * @param  callable  $callback
     * @param  int  $page
     * @param  int  $perPage
     * @return array
     */
    public function rememberPaginated(string $key, $ttl, callable $callback, int $page = 1, int $perPage = 15): array
    {
        $cacheKey = $this->generateKey($key, ['page' => $page, 'per_page' => $perPage]);
        
        return $this->remember($cacheKey, $ttl, function () use ($callback, $page, $perPage) {
            $result = $callback();
            
            if ($result instanceof \Illuminate\Pagination\LengthAwarePaginator) {
                return [
                    'data' => $result->items(),
                    'meta' => [
                        'current_page' => $result->currentPage(),
                        'last_page' => $result->lastPage(),
                        'per_page' => $result->perPage(),
                        'total' => $result->total(),
                    ],
                ];
            }
            
            return $result;
        });
    }

    /**
     * Clear paginated cache
     *
     * @param  string  $key
     * @param  int  $maxPages
     * @return int
     */
    public function clearPaginated(string $key, int $maxPages = 100): int
    {
        $cleared = 0;
        
        for ($page = 1; $page <= $maxPages; $page++) {
            for ($perPage = 10; $perPage <= 100; $perPage += 10) {
                $cacheKey = $this->generateKey($key, ['page' => $page, 'per_page' => $perPage]);
                
                if ($this->forget($cacheKey)) {
                    $cleared++;
                }
            }
        }
        
        return $cleared;
    }
}
