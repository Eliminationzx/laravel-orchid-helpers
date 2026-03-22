<?php

declare(strict_types=1);

if (!function_exists('array_get')) {
    /**
     * Get an item from an array using "dot" notation.
     *
     * @param array<string|int, mixed> $array
     * @param string|int|null $key
     * @param mixed $default
     * @return mixed
     */
    function array_get(array $array, string|int|null $key, mixed $default = null): mixed
    {
        if (is_null($key)) {
            return $array;
        }

        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        if (!str_contains((string) $key, '.')) {
            return $array[$key] ?? $default;
        }

        foreach (explode('.', (string) $key) as $segment) {
            if (is_array($array) && array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return $default;
            }
        }

        return $array;
    }
}

if (!function_exists('array_set')) {
    /**
     * Set an array item to a given value using "dot" notation.
     *
     * @param array<string|int, mixed> $array
     * @param string|int|null $key
     * @param mixed $value
     * @return array<string|int, mixed>
     */
    function array_set(array &$array, string|int|null $key, mixed $value): array
    {
        if (is_null($key)) {
            return $array = $value;
        }

        $keys = explode('.', (string) $key);

        foreach ($keys as $i => $key) {
            if (count($keys) === 1) {
                break;
            }

            unset($keys[$i]);

            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }
}

if (!function_exists('array_has')) {
    /**
     * Check if an item exists in an array using "dot" notation.
     *
     * @param array<string|int, mixed> $array
     * @param string|int|array<string|int> $keys
     * @return bool
     */
    function array_has(array $array, string|int|array $keys): bool
    {
        $keys = (array) $keys;

        if (empty($array) || empty($keys)) {
            return false;
        }

        foreach ($keys as $key) {
            $subKeyArray = $array;

            if (array_key_exists($key, $subKeyArray)) {
                continue;
            }

            foreach (explode('.', (string) $key) as $segment) {
                if (is_array($subKeyArray) && array_key_exists($segment, $subKeyArray)) {
                    $subKeyArray = $subKeyArray[$segment];
                } else {
                    return false;
                }
            }
        }

        return true;
    }
}

if (!function_exists('array_forget')) {
    /**
     * Remove one or many array items from a given array using "dot" notation.
     *
     * @param array<string|int, mixed> $array
     * @param string|int|array<string|int> $keys
     * @return void
     */
    function array_forget(array &$array, string|int|array $keys): void
    {
        $original = &$array;
        $keys = (array) $keys;

        if (empty($keys)) {
            return;
        }

        foreach ($keys as $key) {
            if (array_key_exists($key, $array)) {
                unset($array[$key]);
                continue;
            }

            $parts = explode('.', (string) $key);
            $array = &$original;

            while (count($parts) > 1) {
                $part = array_shift($parts);

                if (isset($array[$part]) && is_array($array[$part])) {
                    $array = &$array[$part];
                } else {
                    continue 2;
                }
            }

            unset($array[array_shift($parts)]);
        }
    }
}

if (!function_exists('array_first')) {
    /**
     * Return the first element in an array passing a given truth test.
     *
     * @param array<string|int, mixed> $array
     * @param callable|null $callback
     * @param mixed $default
     * @return mixed
     */
    function array_first(array $array, ?callable $callback = null, mixed $default = null): mixed
    {
        if (is_null($callback)) {
            if (empty($array)) {
                return value($default);
            }

            foreach ($array as $item) {
                return $item;
            }
        }

        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }

        return value($default);
    }
}

if (!function_exists('array_last')) {
    /**
     * Return the last element in an array passing a given truth test.
     *
     * @param array<string|int, mixed> $array
     * @param callable|null $callback
     * @param mixed $default
     * @return mixed
     */
    function array_last(array $array, ?callable $callback = null, mixed $default = null): mixed
    {
        if (is_null($callback)) {
            return empty($array) ? value($default) : end($array);
        }

        $reversed = array_reverse($array, true);

        foreach ($reversed as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }

        return value($default);
    }
}

if (!function_exists('array_where')) {
    /**
     * Filter the array using the given callback.
     *
     * @param array<string|int, mixed> $array
     * @param callable $callback
     * @return array<string|int, mixed>
     */
    function array_where(array $array, callable $callback): array
    {
        return array_filter($array, $callback, ARRAY_FILTER_USE_BOTH);
    }
}

if (!function_exists('array_wrap')) {
    /**
     * If the given value is not an array, wrap it in one.
     *
     * @param mixed $value
     * @return array<mixed>
     */
    function array_wrap(mixed $value): array
    {
        if (is_null($value)) {
            return [];
        }

        return is_array($value) ? $value : [$value];
    }
}

if (!function_exists('array_pluck')) {
    /**
     * Pluck an array of values from an array.
     *
     * @param array<string|int, mixed> $array
     * @param string|int $value
     * @param string|int|null $key
     * @return array<string|int, mixed>
     */
    function array_pluck(array $array, string|int $value, string|int|null $key = null): array
    {
        $results = [];

        foreach ($array as $item) {
            $itemValue = data_get($item, $value);

            if (is_null($key)) {
                $results[] = $itemValue;
            } else {
                $itemKey = data_get($item, $key);

                if (is_object($itemKey) && method_exists($itemKey, '__toString')) {
                    $itemKey = (string) $itemKey;
                }

                $results[$itemKey] = $itemValue;
            }
        }

        return $results;
    }
}

if (!function_exists('array_sort_by')) {
    /**
     * Sort the array by the given callback or attribute name.
     *
     * @param array<string|int, mixed> $array
     * @param callable|string $callback
     * @param int $options
     * @param bool $descending
     * @return array<string|int, mixed>
     */
    function array_sort_by(array $array, callable|string $callback, int $options = SORT_REGULAR, bool $descending = false): array
    {
        $results = [];

        foreach ($array as $key => $value) {
            $results[$key] = is_callable($callback) ? $callback($value, $key) : data_get($value, $callback);
        }

        $descending ? arsort($results, $options) : asort($results, $options);

        foreach (array_keys($results) as $key) {
            $results[$key] = $array[$key];
        }

        return $results;
    }
}

if (!function_exists('array_to_css_classes')) {
    /**
     * Conditionally compile classes from an array into a CSS class list.
     *
     * @param array<string, bool> $array
     * @return string
     */
    function array_to_css_classes(array $array): string
    {
        $classList = [];

        foreach ($array as $class => $constraint) {
            if (is_numeric($class)) {
                $classList[] = $constraint;
            } elseif ($constraint) {
                $classList[] = $class;
            }
        }

        return implode(' ', $classList);
    }
}

if (!function_exists('array_to_attributes')) {
    /**
     * Convert a string keyed array to HTML attributes.
     *
     * @param array<string, mixed> $array
     * @return string
     */
    function array_to_attributes(array $array): string
    {
        $attributes = [];

        foreach ($array as $key => $value) {
            $element = $key;

            if (!is_null($value)) {
                $element .= '="' . htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8', true) . '"';
            }

            $attributes[] = $element;
        }

        return implode(' ', $attributes);
    }
}

if (!function_exists('array_is_assoc')) {
    /**
     * Determines if an array is associative.
     *
     * An array is "associative" if it doesn't have sequential numerical keys beginning with zero.
     *
     * @param array<string|int, mixed> $array
     * @return bool
     */
    function array_is_assoc(array $array): bool
    {
        $keys = array_keys($array);

        return array_keys($keys) !== $keys;
    }
}

if (!function_exists('array_dot')) {
    /**
     * Flatten a multi-dimensional associative array with dots.
     *
     * @param array<string|int, mixed> $array
     * @param string $prepend
     * @return array<string, mixed>
     */
    function array_dot(array $array, string $prepend = ''): array
    {
        $results = [];

        foreach ($array as $key => $value) {
            if (is_array($value) && !empty($value)) {
                $results = array_merge($results, array_dot($value, $prepend . $key . '.'));
            } else {
                $results[$prepend . $key] = $value;
            }
        }

        return $results;
    }
}

if (!function_exists('array_undot')) {
    /**
     * Expand a flattened array with dots to multi-dimensional array.
     *
     * @param array<string, mixed> $array
     * @return array<string|int, mixed>
     */
    function array_undot(array $array): array
    {
        $results = [];

        foreach ($array as $key => $value) {
            array_set($results, $key, $value);
        }

        return $results;
    }
}

if (!function_exists('array_only')) {
    /**
     * Get a subset of the items from the given array.
     *
     * @param array<string|int, mixed> $array
     * @param array<string|int> $keys
     * @return array<string|int, mixed>
     */
    function array_only(array $array, array $keys): array
    {
        return array_intersect_key($array, array_flip($keys));
    }
}

if (!function_exists('array_except')) {
    /**
     * Get all of the given array except for a specified array of keys.
     *
     * @param array<string|int, mixed> $array
     * @param array<string|int> $keys
     * @return array<string|int, mixed>
     */
    function array_except(array $array, array $keys): array
    {
        return array_diff_key($array, array_flip($keys));
    }
}